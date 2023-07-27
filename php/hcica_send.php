<?php

include_once "bd_StoreControl.php";

$whsCica = $_GET["whsCica"];
$fec = $_GET["fec"];



$sentencia = $db->prepare("update cica set [status]='FIN' where CiCa.fecha=? and CiCa.fk_ID_almacen=? ;");
$sentencia->execute([$fec, $whsCica ]);

$result = $sentencia->rowCount();

header("Location: ../hcica.php");

