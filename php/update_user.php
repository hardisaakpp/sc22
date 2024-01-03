<?php

if (!isset($_GET["idcab"])) {
    echo "Falta el parámetro id.";
    exit;
  }
  
  
$Username = $_POST["username"];
$password = $_POST["password"];
$items = $_POST["items"];
if (isset($_POST["conteo"])) {
    $conteo = $_POST["conteo"];
}else{
    $conteo = 0;
}
$whsCierre = $_POST["whsCierre"];
$whsInvs = $_POST["whsInvs"];
$whsHorario = $_POST["whsHorario"];
$whsTransitorio = $_POST["whsTransitorio"];
$codTimeSoft = $_POST["codTimeSoft"];
$perfil = $_POST["radios"];
$email1 = $_POST["Email1"];
$email2 = $_POST["Email2"];
$id = $_GET["idcab"];
include_once "bd_StoreControl.php";
$sentencia = $db->prepare("
UPDATE [dbo].[users]
        SET [username] = ?, 
        [password] = ?,
        [articulosContar] = ?,
    [realizaConteo] = ?,
    [fk_ID_almacen_cierre] = ?,
    [fk_ID_almacen_invs] = ?,
    [fk_ID_almacen_turemp] = ?,
    [fk_ID_almacen_transitorio] = ?,
    [Timesoft_CentroCosto] = ?,
    [perfil] = ?,
    [email] = ?,
    [emailSuper] = ?
        
    where [id] = ?");

$resultado = $sentencia ->execute([
    $Username, 
    $password,
    $items,
    $conteo,
    $whsCierre,
    $whsInvs,
    $whsHorario,
    $whsTransitorio,
    $codTimeSoft,
    $perfil,
    $email1,
    $email2,
    $id]);
if ($resultado == true){
    header("Location: ../userL.php");
}
else {
    echo "Algo salió mal. Por favor verifica que la tabla exista, así como el ID del usuario";
}
