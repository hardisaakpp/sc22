<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
//$idconteo = $_GET["idconteo"];
include_once "bd_StoreControl.php";

$sentencia = $db->prepare("
		delete from Stockcab where id=?;
			delete from StockDet where FK_id_StockCab=?;
			delete from StockScan where FK_id_StockCab=?;
			delete from StockLog where FK_id_StockCab=?;
        ");
$sentencia->execute([$id,$id,$id,$id]);
$result = $sentencia->rowCount();

echo $result;

