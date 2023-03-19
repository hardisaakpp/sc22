<?php

$responsable = $_POST["responsable"];
$idcab =$_POST["idcab"];

include_once "bd_StoreControl.php";


    $s1 = $db->prepare("
    update StockCab_TFA set responsable=? where FK_id_StockCab=?
    ");
    $s1->execute([$responsable, $idcab]);

   // $result = $s1->fetch(PDO::FETCH_OBJ);
    

    //echo $res;


?>