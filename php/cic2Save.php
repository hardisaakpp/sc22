<?php
include_once "bd_StoreControl.php";

$id = $_POST['id'];  //ID de cica
$ccaja = $_POST['ccaja'];  //whscode
$fec = $_POST['fec'];
$fk_ID_almacen=$_POST['fk_ID_almacen'];
//parametros CICA
$cresponsable = $_POST['crespons'];
$cobservacion = $_POST['cobs'];

    //guardo cabecera cica
    $sentencia1 = $db->prepare("UPDATE [dbo].[CiC]
                SET [responsable] = ?
                ,[observacion] = ?
                WHERE [fecha] = '" . $fec . "'
                and [caja] = '" . $ccaja . "' 
                and [fk_ID_almacen] = '" . $fk_ID_almacen . "' ");
    $resultado1 = $sentencia1->execute([$cresponsable,$cobservacion]);


//parametros GRID 
$idcicasap = $_POST['idcicasap'];
$valRec = $_POST['valRec'];
$valOnline = $_POST['valOnline'];
$valDatMed = $_POST['valDatMed'];
$refOnline = $_POST['refOnline'];
$refDatMed = $_POST['refDatMed'];

$total = count($idcicasap);
//guardando
for($i=0;$i<$total;$i++){

    $sentencia1 = $db->prepare("UPDATE [dbo].[cicUs]
                SET [valRec] = ?, [valOnline]=?, [valPinpadOff]=?
                    ,[refOnline] = ?, [refPinpadOff]=?
                WHERE [id] = '" . $idcicasap[$i] . "' ");
    $resultado1 = $sentencia1->execute([$valRec[$i],$valOnline[$i],$valDatMed[$i],$refOnline[$i],$refDatMed[$i]]);


                        
    if ($resultado1 === true) {
        echo "ok ".$idcicasap[$i]. " </br>" ;
    } else {
        echo "Algo salió mal </br>";
    }

}


header("Location:../cic2U.php?id=".$id);
?> 