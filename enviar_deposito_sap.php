<?php
header('Content-Type: application/json');
include_once "php/bd_StoreControl.php";

// Ruta absoluta segura para el log
define('LOG_PATH', __DIR__ . "/log_integracion.txt");

function logError($mensaje) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $mensaje . PHP_EOL;
    file_put_contents(LOG_PATH, $log, FILE_APPEND);
}

// Log inicial del script
logError("===> Script iniciado. Método: " . $_SERVER['REQUEST_METHOD']);

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    logError("Método no permitido.");
    exit;
}

// Obtener input JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    logError("ID no proporcionado en la solicitud.");
    exit;
}

logError("Procesando integración para ID: $id");

try {
    // Obtener depósito
    $stmt = $db->prepare("SELECT * FROM DepositosTiendas WHERE id = ?");
    $stmt->execute([$id]);
    $deposito = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    logError("Error al consultar depósito: " . $e->getMessage());
    echo json_encode(['error' => 'Error al acceder a la base de datos']);
    exit;
}

if (!$deposito) {
    echo json_encode(['error' => 'Depósito no encontrado']);
    logError("Depósito ID $id no encontrado.");
    exit;
}

if ($deposito['creadoSAP']) {
    echo json_encode(['error' => 'Este depósito ya fue integrado']);
    logError("Depósito ID $id ya fue integrado.");
    exit;
}

// Armar datos a enviar

    $data = [
        "DepositDate"        => $deposito["DepositDate"],
        "DepositAccount"     => $deposito["DepositAccount"],
        "DepositCurrency"    => $deposito["DepositCurrency"],
        "DepositType"        => $deposito["DepositType"],
        "AllocationAccount"  => $deposito["AllocationAccount"],
        "TotalLC"            => floatval($deposito["TotalLC"]),
        "JournalRemarks"     => "CIERRE CAJA " . $deposito["U_Fecha"] . " " . $deposito["U_WhsCode"],
        "BankReference"      => $deposito["U_Ref_Bancar"],
        "U_IXX_REF_BANCARIA" => $deposito["U_WhsCode"]
    ];

logError("Datos preparados para enviar: " . json_encode($data));

// Enviar al endpoint de SAP
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://192.168.2.12:8086/api/Deposit',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'User-Agent: Apidog/1.0.0 (https://apidog.com)',
        'Content-Type: application/json',
        'Accept: */*',
        'Connection: keep-alive'
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);

// Log del resultado de cURL
if ($error) {
    logError("❌ cURL error: $error");
    echo json_encode(['error' => 'Error de red al conectar con SAP']);
    exit;
}

logError("✅ Respuesta SAP (HTTP $httpCode): $response");

// Verificar éxito
if ($httpCode === 200 || $httpCode === 201) {
    try {
        $upd = $db->prepare("UPDATE DepositosTiendas SET creadoSAP = 1 WHERE id = ?");
        $upd->execute([$id]);
        logError("✅ Depósito ID $id marcado como integrado.");
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        logError("❌ Error al actualizar base de datos: " . $e->getMessage());
        echo json_encode(['error' => 'Error al actualizar estado en base de datos']);
    }
} else {
    logError("❌ Error SAP (HTTP $httpCode): $response");
    echo json_encode([
        'error' => 'Error al integrar con SAP',
        'httpCode' => $httpCode,
        'response' => $response
    ]);
}
