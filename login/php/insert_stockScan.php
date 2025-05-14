<?php
include_once "bd_StoreControl.php";

//$variable = $_POST['page'];

//echo $_POST['barcode']."<br>";
$barcode = $_POST['barcode'];
$id_user = $_POST['id_user'];
$id_cab = $_POST['id_cab'];

//$fecha = new DateTime();
//echo $fecha->getTimestamp();


$sentencia = $db->prepare("INSERT INTO [dbo].[StockScan] ([id_user],[barcode],[fk_id_stockCab]) 
      VALUES ('".$id_user."','".$barcode."','".$id_cab."');");
$resultado = $sentencia->execute(); # Pasar en el mismo orden de los ?

echo $barcode."<br>";

?>