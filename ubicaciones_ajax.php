<?php
// ubicaciones_ajax.php
include_once "php/bd_StoreControl.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["whsBodeg"])) {
    echo json_encode([]);
    exit;
}

$whsBodeg = $_SESSION["whsBodeg"];

// Obtener el WhsCode real desde Almacen
$sentenciaWhs = $db->prepare("SELECT cod_almacen FROM Almacen WHERE id = ?");
$sentenciaWhs->execute([$whsBodeg]);
$rowWhs = $sentenciaWhs->fetch(PDO::FETCH_ASSOC);
$whsCode = $rowWhs ? $rowWhs['cod_almacen'] : '';
echo $whsCode;
if (!$whsCode) {
    echo json_encode([]);
    exit;
}

// Buscar ubicaciones
$sentencia = $db->prepare("SELECT AbsEntry, BinCode FROM Ubicaciones WHERE WhsCode = ? ORDER BY BinCode");
$sentencia->execute([$whsCode]);
$ubicaciones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Agregar whsCode al primer registro para debug en frontend
if (count($ubicaciones) > 0) {
    $ubicaciones[0]['whsCode'] = $whsCode;
}

echo json_encode($ubicaciones);
