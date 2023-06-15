<?php
include_once "bd_StoreControl.php";

$id = $_POST['id'];  //ID de cica
$ccaja = $_POST['ccaja'];  //whscode
$fec = $_POST['fec'];

//parametros CICA
$cresponsable = $_POST['crespons'];
$cobservacion = $_POST['cobs'];

    //guardo cabecera cica
    $sentencia1 = $db->prepare("UPDATE [dbo].[CiCa]
                SET [responsable] = ?
                ,[observacion] = ?
                WHERE [fecha] = '" . $fec . "'
                and [caja] = '" . $ccaja . "' ");
    $resultado1 = $sentencia1->execute([$cresponsable,$cobservacion]);

/*
$caja = $_POST['caja'];
$forPag = $_POST['forPag'];
$valSAP = $_POST['valSAP'];



$refOnline = $_POST['refOnline'];
$valPinPad = $_POST['valPinPad'];
$refPinPad = $_POST['refPinPad'];

$refDatMed = $_POST['refDatMed'];

$total = count($forPag);
*/

//parametros GRID 
$idcicasap = $_POST['idcicasap'];
$valRec = $_POST['valRec'];
$valOnline = $_POST['valOnline'];
$valDatMed = $_POST['valDatMed'];

$total = count($idcicasap);
//guardando
for($i=0;$i<$total;$i++){
/*
    $sentencia = $db->prepare("EXEC sp_setCiCaSys '".$caja[$i]."','".$fec[$i]."','".$forPag[$i]."','".$valSAP[$i]."','".$valRec[$i]."','"
                        .$valOnline[$i]."','".$refOnline[$i]."','".$valPinPad[$i]."','"
                        .$refPinPad[$i]."','".$valDatMed[$i]."','".$refDatMed[$i]."'");
    $resultado = $sentencia->execute();
*/

    $sentencia1 = $db->prepare("UPDATE [dbo].[CiCaSAP]
                SET [valRec] = ?, [valOnline]=?, [valPinpadOff]=?
                WHERE [id] = '" . $idcicasap[$i] . "' ");
    $resultado1 = $sentencia1->execute([$valRec[$i],$valOnline[$i],$valDatMed[$i]]);


                        
    if ($resultado === true) {
        echo "ok ".$idcicasap[$i]. " </br>" ;
    } else {
        echo "Algo salió mal </br>";
    }

}


/*
for($i=0;$i<$total;$i++){

    $sentencia = $db->prepare("EXEC sp_setCiCaSys '".$caja[$i]."','".$fec[$i]."','".$forPag[$i]."','".$valSAP[$i]."','".$valRec[$i]."','"
                        .$valOnline[$i]."','".$refOnline[$i]."','".$valPinPad[$i]."','"
                        .$refPinPad[$i]."','".$valDatMed[$i]."','".$refDatMed[$i]."'");
   
    //echo "EXEC sp_setCiCaSys '".$caja[$i]."','".$fec[$i]."','".$forPag[$i]."','".$valSAP[$i]."','".$valRec[$i]."','"
    //.$valOnline[$i]."','".$refOnline[$i]."','".$valPinPad[$i]."','"
    //.$refPinPad[$i]."','".$valDatMed[$i]."','".$refDatMed[$i]."'";

                        $resultado = $sentencia->execute();
                        
    if ($resultado === true) {
        echo "ok ".$forPag[$i]. " </br>" ;
    } else {
        echo "Algo salió mal </br>";
    }

//$query .= "('" . $nom_part[$i] . "','" . $cantid[$i] . "','" . $prop[$i] . "')";

//$query .= ($i<$total-1) ? "," : "";
//echo $fec[$i];
}
*/
///////////////////////////////////header("Location: cica.php");
//echo $query;

//mysql_query($query) or die(mysql_error());
header("Location:../cicaU.php?id=".$id);
?> 