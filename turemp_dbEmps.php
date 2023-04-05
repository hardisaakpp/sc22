<?php

include_once "php/bd_StoreControl.php";
//$variable = $_POST['page'];

//echo $_POST['barcode']."<br>";
$cedula= $_POST['cedula'];
$nombre= $_POST['nombre'];
$apellido= $_POST['apellido'];
$terminal= $_POST['terminal'];
$id_alm= $_POST['id_alm'];
$mes= $_POST['mes'];
$year = $_POST['year']; 
$sueldo = $_POST['sueldo']; 


$sentencia = $db->prepare("

exec sp_newTurEmp '".$id_alm."','".$cedula."','".$nombre."','".$apellido."','".$mes."','".$year."','".$terminal."','".$sueldo."';   
   
   ");
$resultado = $sentencia->execute(); # Pasar en el mismo orden de los ?

?>