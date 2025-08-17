<?php

if (!isset($_GET["idcab"])) {
    echo "Falta el parámetro id.";
    exit;
}

$Username = isset($_POST["username"]) ? $_POST["username"] : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';
$items = isset($_POST["items"]) ? $_POST["items"] : '';
$conteo = isset($_POST["conteo"]) ? 1 : 0; // Convert checkbox to bit-compatible value
$whsCierre = isset($_POST["whsCierre"]) ? $_POST["whsCierre"] : 0;
$whsInvs = isset($_POST["whsInvs"]) ? $_POST["whsInvs"] : 0;
$whsBodega = isset($_POST["whsBodega"]) ? $_POST["whsBodega"] : 0;
$whsTransitorio = isset($_POST["whsTransitorio"]) ? $_POST["whsTransitorio"] : 0;
$whsCD = isset($_POST["whsCD"]) ? $_POST["whsCD"] : 0;
$codTimeSoft = isset($_POST["codTimeSoft"]) ? $_POST["codTimeSoft"] : '';
$perfil = isset($_POST["radios"]) ? $_POST["radios"] : '';
$email1 = isset($_POST["Email1"]) ? $_POST["Email1"] : '';
$email2 = isset($_POST["Email2"]) ? $_POST["Email2"] : '';
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
        [fk_ID_almacen_bodeg] = ?,
        [fk_ID_almacen_transitorio] = ?,
        [fk_ID_almacen_CD] = ?,
        [perfil] = ?,
        [email] = ?,
        [emailSuper] = ?
    WHERE [id] = ?
");

$resultado = $sentencia->execute([
    $Username, 
    $password,
    $items,
    $conteo,
    $whsCierre,
    $whsInvs,
    $whsBodega,
    $whsTransitorio,
    $whsCD,
    $perfil,
    $email1,
    $email2,
    $id
]);

if ($resultado === true) {
    header("Location: ../userL.php");
} else {
    echo "Algo salió mal. Por favor verifica que la tabla exista, así como el ID del usuario";
}
?>
