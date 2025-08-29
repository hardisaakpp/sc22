<?php
include_once "php/bd_StoreControl.php"; // o tu conexión a la DB

$idCab = $_POST['idCab'] ?? 0;

$stmt = $db->prepare("SELECT ItemCode, Quantity FROM rep_det WHERE Quantity > 0 AND fk_id_cab = ?");
$stmt->execute([$idCab]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($detalles);
