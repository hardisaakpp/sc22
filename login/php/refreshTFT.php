<?php

//$userId = $_POST["userId"];
$idcab = $_GET["idcab"];

include_once "bd_StoreControl.php";

$sentencia = $db->prepare("exec [spActualizaInventarioTFT] ". $idcab .";");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute();

$result = $sentencia->rowCount();

//echo $result ;

header("Location: ../TTrefresh.php");
