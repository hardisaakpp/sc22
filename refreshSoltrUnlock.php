<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
//$idcab = $_GET["idcab"];
include_once "php/bd_StoreControl.php";
$sentencia = $db->prepare("update StockCab_ST set estado='INI' where solicitud=".$id . " ");
$resultado = $sentencia->execute([$id]);
if ($resultado === true) {
    header("Location: soltr.php?sx=".$id );
} else {
    echo "Algo salió mal";
}
