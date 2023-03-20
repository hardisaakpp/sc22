<?php

include_once "bd_StoreControl.php";

$idalm = $_POST['idalm'];


//echo $responsable;
//echo $estado;
$sentencia1 = $db->prepare("execute sp_newTfT ".$idalm." ");
$resultado1 = $sentencia1->execute();

header("Location: ../loadTT.php");


?> 