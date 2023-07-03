<?php

include_once "bd_StoreControl.php";

$id = $_POST['id'];
$conteo = $_POST['conteo'];
$responsable = $_POST['responsable'];
$estado = $_POST['estado'];
$idcab = $_POST['idcab'];

$total = count($conteo);

//echo $responsable;
//echo $estado;


//INSERTA Y MODIFICA ESTADO SEGUN
if ($estado=='CONTEO') {
    // ACTUALIZA EL STOCK DE INVENTARIO
        $sentencia1 = $db->prepare("DECLARE @IDCAB INT;
        SET @IDCAB=?;

        exec [spActualizaInventario] @IDCAB;

        IF not EXISTS
        (
            SELECT *
            FROM StockCab_tfa
            WHERE fk_id_StockCab=@IDCAB
        )
            BEGIN
                insert into StockCab_tfa (fk_id_StockCab) values (@IDCAB)
        END;
        UPDATE StockCab_TFA set responsable=? where fk_id_StockCab=@IDCAB ");
        $resultado1 = $sentencia1->execute([$idcab,$responsable]);
    for($i=0;$i<$total;$i++){

        $sentencia = $db->prepare("UPDATE StockDet 
                                    set conteo=?, reconteo=?, estado=(CASE WHEN stock<>? THEN 'REC' else 'FIN' end)
                                    where id=?;");
        $resultado = $sentencia->execute([$conteo[$i],$conteo[$i],$conteo[$i],$id[$i]]);
    }
}else {

    $sentencia1 = $db->prepare("DECLARE @IDCAB INT;
    SET @IDCAB=?;

    exec [spActualizaInventario] @IDCAB;

    IF not EXISTS
    (
        SELECT *
        FROM StockCab_tfa
        WHERE fk_id_StockCab=@IDCAB
    )
        BEGIN
            insert into StockCab_tfa (fk_id_StockCab) values (@IDCAB)
    END;
    UPDATE StockCab_TFA set responsable=? where fk_id_StockCab=@IDCAB ");
    $resultado1 = $sentencia1->execute([$idcab,$responsable]);

    for($i=0;$i<$total;$i++){

        $sentencia = $db->prepare("UPDATE StockDet 
                                    set reconteo=?, estado='FIN'
                                    where id=?;");
        $resultado = $sentencia->execute([$conteo[$i],$id[$i]]);
    }
}



header("Location: ../tfaD.php");


?> 