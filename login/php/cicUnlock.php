<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
$fecha = $_GET["fecha"];
include_once "bd_StoreControl.php";

$sentencia = $db->prepare("

declare @estado int 
set @estado = (Select top 1 cerrado from CiC cic where cic.fk_ID_almacen=? and cic.fecha=?)
if (@estado=1)
    update cic set cerrado=0  where fk_ID_almacen=".$id." and fecha='".$fecha."';
else
    update cic set cerrado=1 where fk_ID_almacen=".$id." and fecha='".$fecha."';
");
$sentencia->execute([$id, $fecha]);
$result = $sentencia->rowCount();

echo $result;

