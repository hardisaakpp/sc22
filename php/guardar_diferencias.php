<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

include_once "bd_StoreControl.php";

// Recibir JSON desde fetch
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['idcab']) || !isset($data['codigos'])) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

$idcab = (int)$data['idcab'];
$codigos = $data['codigos']; // array de códigos

try {
    // 1. Eliminar los existentes para esta cabecera
    $stmtDel = $db->prepare("DELETE FROM TransferenciasDiferencias WHERE id_TrCab = ?");
    $stmtDel->execute([$idcab]);

    // 2. Insertar los nuevos sin repetir
    $stmtIns = $db->prepare("INSERT INTO TransferenciasDiferencias (id_TrCab, CodeBars, datetime) VALUES (?, ?, GETDATE())");

    $codigosUnicos = array_unique($codigos);
    foreach ($codigosUnicos as $c) {
        $stmtIns->execute([$idcab, $c]);
    }

    echo json_encode(["status" => "success", "message" => "Códigos guardados correctamente."]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

exit(); // Asegura que no se envíe HTML adicional
