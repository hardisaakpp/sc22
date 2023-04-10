<?php
//echo "Estás usando Ajax <br> ".$_POST["Nombre"]." ".$_POST["Mensaje"];



include_once "php/bd_StoreControl.php";

$idTuremp = $_POST['idTuremp'];
$dateKey = $_POST['dateKey'];
$codTurno = $_POST['codTurno'];
$descripcio = $_POST['descripcio'];
$horasLab = $_POST['horasLab'];
$o1 = $_POST['o1'];
$o2 = $_POST['o2'];
$o3 = $_POST['o3'];
$o4 = $_POST['o4'];
$o5 = $_POST['o5'];
$o6 = $_POST['o6'];

$sentencia1 = $db->prepare("UPDATE turem_day SET cod_turno=? , des_turno=? , horlab_turno=?
where fk_DateKey='" . $dateKey . "' and fk_id_turem='" . $idTuremp . "' ");
$resultado1 = $sentencia1->execute([$codTurno, $descripcio, $horasLab]);


$sentencia2 = $db->prepare("UPDATE turem SET o1=?,o2=?,o3=?,o4=?,o5=?,o6=?
where id='" . $idTuremp . "' ");
$resultado2 = $sentencia2->execute([$o1, $o2, $o3, $o4, $o5, $o6]);

//echo "ok, se guardo.";
/*
if ($resultado1 === true) {
    echo "ok ".$forPag[$i]. " </br>" ;
} else {
    echo "Algo salió mal </br>";
}
*/
?>