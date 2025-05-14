<?php
include_once "bd_StoreControl.php";

//$variable = $_POST['page'];

//echo $_POST['barcode']."<br>";
$barcode = $_POST['barcode'];
$id_user = $_POST['id_user'];
$ID_CONTEO = $_POST['ID_CONTEO'];






$sentencia = $db->prepare("
    INSERT INTO [dbo].[StockScan] ([id_user],[barcode],[fk_id_stockCab]) 
    VALUES
            (?
            ,?
            ,?);");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute([$id_user, $barcode, $ID_CONTEO]);

$result = $sentencia->rowCount();

echo $result;

?>