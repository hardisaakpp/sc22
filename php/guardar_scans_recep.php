<?php
include_once "bd_StoreControl.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Datos no recibidos"]);
    exit;
}

$responsable = $data['responsable'] ?? '';
$idcab = $data['idcab'] ?? null;
$items = $data['items'] ?? [];

if (!$responsable || !$idcab) {
    echo json_encode(["status" => "error", "message" => "Responsable e idcab son obligatorios"]);
    exit;
}

try {
    // Guardar detalle
    foreach ($items as $item) {
        $stmt = $db->prepare("UPDATE [dbo].[TransferenciasDetalle] 
                              SET [Scan] = :scan 
                              WHERE [Id] = :id");
        $stmt->execute([
            ':scan' => $item['scan'],
            ':id'   => $item['id']
        ]);
    }

    // Guardar responsable en cabecera
    $stmt = $db->prepare("UPDATE [dbo].[TransferenciasCabecera] 
                          SET Responsable = :responsable 
                          WHERE Id = :idcab");
    $stmt->execute([
        ':responsable' => $responsable,
        ':idcab'       => $idcab
    ]);

    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
