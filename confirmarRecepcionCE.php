<?php
include_once "php/bd_StoreControl.php";

if (!isset($_GET["idcab"]) || !isset($_GET["docnum"]) || !isset($_GET["id"])) {
    exit("Faltan parámetros.");
}

$idcab  = intval($_GET["idcab"]);   // grupo
$docnum = intval($_GET["docnum"]);  // DocNum_Sot
$id     = intval($_GET["id"]);      // id para abrir en cediPickT

// Ejecutar update
$sql = "UPDATE [dbo].[ced_groupsotdetCE] 
        SET Scan = Quantity 
        WHERE fk_idgroup = :idcab AND [DocNum_Sot] = :docnum";

$stmt = $db->prepare($sql);
$stmt->execute([":idcab"=>$idcab, ":docnum"=>$docnum]);

// Redirigir a cediPickT
header("Location: cediPickTCE.php?idcab=".$id);
exit;
