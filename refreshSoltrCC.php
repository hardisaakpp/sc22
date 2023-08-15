<?php

if (!isset($_POST["id"])) {
    exit();
}
echo 'OK';
$id = $_POST["id"];

$cc = $_POST["cc"];
//$idcab = $_GET["idcab"];
include_once "php/bd_StoreControl.php";
$sentencia = $db->prepare("update StockCab_ST set cartones=".$cc." where solicitud=".$id . " ");
$resultado = $sentencia->execute([$id]);
echo 'OKOK';
if ($resultado === true) {
    header("Location: soltr.php?sx=".$id );
} else {
    echo "Algo salió mal";
}
