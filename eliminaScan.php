<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
$idcab = $_GET["idcab"];
include_once "php/bd_StoreControl.php";
$sentencia = $db->prepare("DELETE FROM stockScan WHERE id = ?;");
$resultado = $sentencia->execute([$id]);
if ($resultado === true) {
    header("Location: soltrDel.php?sx=".$idcab );
} else {
    echo "Algo salió mal";
}
