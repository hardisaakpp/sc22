<?php
include_once "bd_StoreControl.php";

$barcode = $_POST['barcode'];
$id_user = $_POST['id_user'];
$ID_CONTEO = $_POST['ID_CONTEO'];
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

$sentencia = $db->prepare("EXEC insertar_codigo_barras ?, ?, ?, ?");
$sentencia->execute([$id_user, $barcode, $ID_CONTEO, $cantidad]);

echo "OK";
?>
