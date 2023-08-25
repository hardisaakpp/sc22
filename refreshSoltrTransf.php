<?php

if (!isset($_GET["id"])) {
    exit();
}

$id = $_GET["id"];

include_once "php/bd_StoreControl.php";
$myArray = explode(',', $id);

for ($i=0; $i < count($myArray); $i++) { 
    //echo $myArray[$i];
    $sentencia = $db->prepare("exec [sp_refreshSolTrT] ".$myArray[$i] . " ");
    $resultado = $sentencia->execute([$id]);
}


//$idcab = $_GET["idcab"];


if ($resultado === true) {
    //header("Location: soltrL.php?sx=".$id );
    header("Location: soltrL.php");
} else {
    echo "Algo salió mal";
}
