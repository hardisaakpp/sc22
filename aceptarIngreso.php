<?php
session_start();
include_once "php/bd_StoreControl.php";

// Validar que llegue la OC y Responsable
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
    // 1️⃣ Simular llamada al API
    // -------------------------------
    // Aquí colocas tu llamada real al API. Por ahora simulamos:
    $apiExito = true; // cambiar a false para probar error

    if ($apiExito) {
        $resultadoAPI = "PRUEBA Ingreso aceptado correctamente por API.";
    } else {
        throw new Exception("Error de conexión al API.");
    }

} catch (Exception $e) {
    $resultadoAPI = "Error: " . $e->getMessage();
}

// -------------------------------
// 2️⃣ Registrar en la tabla de log
// -------------------------------
try {
    $sqlLog = "INSERT INTO OC_Aceptacion_Log (Usuario, Responsable, DocNum, ResultadoAPI)
               VALUES (:Usuario, :Responsable, :DocNum, :ResultadoAPI)";
    $stmt = $db->prepare($sqlLog);
    $stmt->execute([
        ':Usuario'     => $Usuario,
        ':Responsable' => $Responsable,
        ':DocNum'      => $DocNum,
        ':ResultadoAPI'=> $resultadoAPI
    ]);
} catch (PDOException $e) {
    // Si hay error en SQL Server, igualmente lo mostramos
    die("Error al registrar el log: " . $e->getMessage());
}

// -------------------------------
// 3️⃣ Redirigir de nuevo a la OC con mensaje
// -------------------------------
header("Location: odc.php?oc=$DocNum&msg=" . urlencode($resultadoAPI));
exit;
