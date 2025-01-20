<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
$fecha = $_GET["fecha"];
include_once "bd_StoreControl.php";

$sentencia = $db->prepare("

  
declare @estado nvarchar(2) 
set @estado = (Select top 1 tipo from [StockCab] cic where cic.id=?)
if (@estado='TT')
    update [StockCab] set tipo='TP'  where id=".$id." ;
else
   update [StockCab] set tipo='TT'  where id=".$id." ;

");
$sentencia->execute([$id]);
$result = $sentencia->rowCount();

echo $result;

