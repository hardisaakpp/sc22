<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];
$idconteo = $_GET["idconteo"];
include_once "bd_StoreControl.php";
$sentencia = $db->prepare("  delete FROM [STORECONTROL].[dbo].[turem] where id= ?;");
$resultado = $sentencia->execute([$id]);
if ($resultado === true) {
    header("Location: ../turEmpY.php");
} else {
    echo "Algo salió mal";
}
