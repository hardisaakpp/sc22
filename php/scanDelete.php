<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
$idconteo = $_GET["idconteo"];
include_once "bd_StoreControl.php";

$sentencia = $db->prepare("delete from StockScan where id=?;");
$sentencia->execute([$id]);
$result = $sentencia->rowCount();

echo $result;

