<?php

$WhsCode = $_POST["WhsCode"];
$Quantity = $_POST["Quantity"];


include_once "bd_StoreControl.php";

$s1 = $db->prepare("
update StockDet set conteo=(CASE WHEN estado='INI' then ? else conteo end), reconteo=? where id=?;
");
$s1->execute([$Quantity,$Quantity,$WhsCode]);

/*
if (Status==1) {
    $s1 = $db->prepare("
    update StockDet set conteo=? where id=?;
    ");
    $s1->execute([$Quantity,$WhsCode]);
   // echo 'CONTEO';
} else {
    $s1 = $db->prepare("
    update StockDet set reconteo=? where id=?;
    ");
    $s1->execute([$Quantity,$WhsCode]);
   // echo 'RECONTEO';
}
*/

    

   // $result = $s1->fetch(PDO::FETCH_OBJ);
    

    //echo $res;


?>