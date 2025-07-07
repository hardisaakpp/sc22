<?php
// get_warehouse_code.php
include_once "php/bd_StoreControl.php";
session_start();

header('Content-Type: application/json');

// Permitir obtener por parámetro id o por sesión
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_SESSION["whsBodeg"]) ? intval($_SESSION["whsBodeg"]) : 0);

if (!$id) {
    echo json_encode([]);
    exit;
}

// Obtener el WhsCode real desde Almacen
$sentenciaWhs = $db->prepare("SELECT cod_almacen FROM Almacen WHERE id = ?");
$sentenciaWhs->execute([$id]);
$rowWhs = $sentenciaWhs->fetch(PDO::FETCH_ASSOC);
$whsCode = $rowWhs ? $rowWhs['cod_almacen'] : '';

if (!$whsCode) {
    echo json_encode([]);
    exit;
}

echo json_encode(['cod_almacen' => $whsCode]);
