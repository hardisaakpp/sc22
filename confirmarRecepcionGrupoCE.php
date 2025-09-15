<?php
include_once "php/bd_StoreControl.php";

if (!isset($_GET["idcab"])) {
    exit("Falta parámetro idcab.");
}

$idcab = intval($_GET["idcab"]);

// Ejecutar update
$sql = "UPDATE [dbo].[ced_groupsotdetCE] 
        SET Scan = Quantity 
        WHERE fk_idgroup = :idcab";

$stmt = $db->prepare($sql);
$stmt->execute([":idcab"=>$idcab]);

// Volver a la misma página (lista actual)
header("Location: cediGrpLdidCE.php?idcab=".$idcab);
exit;
