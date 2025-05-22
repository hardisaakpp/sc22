<?php
include_once "bd_StoreControl.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$empresa = $data['empresa'];

try {
    $stmt = $db->prepare("SELECT id FROM Almacen WHERE hit_cod_local IS NOT NULL AND hit_cod_local <> 0 AND fk_emp = ?");
    $stmt->execute([$empresa]);
    $almacenes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($almacenes);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
