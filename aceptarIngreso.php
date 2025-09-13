<?php
session_start();
include_once "php/bd_StoreControl.php";

// -------------------------------
// 1️⃣ Validar que llegue la OC y Responsable
// -------------------------------
if (!isset($_POST['DocNum']) || !isset($_POST['Responsable'])) {
    die("Datos incompletos.");
}

$DocNum      = $_POST['DocNum'];
$Responsable = $_POST['Responsable'];
$Usuario     = $_SESSION["idU"] ?? 0;

// Variable para el resultado del API
$resultadoAPI = '';

try {
    // -------------------------------
    // 2️⃣ Consultar líneas de la OC desde tu SP
    // -------------------------------
    $stmt = $db->prepare("EXEC sp_SAP_OC_Todas :DocNum");
    $stmt->execute([':DocNum' => $DocNum]);
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (count($rows) == 0) {
        throw new Exception("No se encontraron registros para la OC $DocNum.");
    }

    // -------------------------------
    // 3️⃣ Tomar la fecha de creación de la OC (desde SQL Server)
    // -------------------------------
    // Asegúrate de que el SP devuelve un campo con la fecha (ej: FechaHoraActualizacion o DocDate)
    $fechaOC = isset($rows[0]->FechaHoraActualizacion) ? substr($rows[0]->FechaHoraActualizacion, 0, 10) : date('Y-m-d');

    // -------------------------------
    // 4️⃣ Construir JSON para el API
    // -------------------------------
    $documentLines = [];
    foreach ($rows as $r) {
        $documentLines[] = [
            'BaseType'      => 22,
            'BaseEntry'     => $r->DocEntry,
            'BaseLine'      => $r->LineNum,
            'ItemCode'      => $r->ItemCode,
            'Quantity'      => $r->Quantity,
            'WarehouseCode' => $r->WhsCode
        ];
    }

    $goodsReceipt = [
        'DocDate'       => $fechaOC, // ✅ Usar la fecha de creación de la OC
        'Comments'      => "Ingreso de mercadería por $Responsable",
        'DocumentLines' => $documentLines
    ];

    // -------------------------------
    // 5️⃣ Llamar al API REST
    // -------------------------------
    $apiUrl = "http://192.168.2.12:8087/api/GoodsReceipt/TEST";

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($goodsReceipt));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Error CURL: $curlError");
    }

    if ($httpCode >= 400) {
        $resultadoAPI = "Error API HTTP $httpCode: $response";
    } else {
        $resultadoAPI = "Ingreso de mercadería aceptado correctamente. API response: $response";
    }

} catch (Exception $e) {
    $resultadoAPI = "Error: " . $e->getMessage();
}

// -------------------------------
// 6️⃣ Registrar en la tabla de log
// -------------------------------
try {
    $sqlLog = "INSERT INTO OC_Aceptacion_Log (Usuario, Responsable, DocNum, ResultadoAPI)
               VALUES (:Usuario, :Responsable, :DocNum, :ResultadoAPI)";
    $stmt = $db->prepare($sqlLog);
    $stmt->execute([
        ':Usuario'      => $Usuario,
        ':Responsable'  => $Responsable,
        ':DocNum'       => $DocNum,
        ':ResultadoAPI' => $resultadoAPI
    ]);
} catch (PDOException $e) {
    die("Error al registrar el log: " . $e->getMessage());
}

// -------------------------------
// 7️⃣ Redirigir de nuevo a la OC con mensaje
// -------------------------------
header("Location: odc.php?oc=$DocNum&msg=" . urlencode($resultadoAPI));
exit;
?>
