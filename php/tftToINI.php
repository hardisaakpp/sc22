<?php

include_once "bd_StoreControl.php";

$idcab = $_GET["idcab"];


$sentencia1 = $db->prepare("update StockDet set scan=0 , conteo=0 where FK_id_StockCab=?;" );
$resultado1 = $sentencia1->execute([$idcab]);

$sentencia = $db->prepare("exec sp_setTFT_scantoini  ?;" );
$resultado = $sentencia->execute([$idcab]);

$sentencia2 = $db->prepare("  declare @idcab int
set @idcab=?
update StockDet set reconteo=conteo, estado='FIN' where stock=conteo and FK_id_StockCab=@idcab
update StockDet set reconteo=conteo, estado='REC' where stock<>conteo and FK_id_StockCab=@idcab;

IF not EXISTS
(
    SELECT *
    FROM StockCab_tfa
    WHERE fk_id_StockCab=@idcab
)
    BEGIN
        insert into StockCab_tfa (fk_id_StockCab) values (@idcab)
END;
" 
);
$resultado2 = $sentencia2->execute([$idcab]);


//$result = $sentencia->rowCount();

//header("Location: ../TTListR.php");
header("Location: ../TTreporteSacnsI.php?idcab=".$idcab);