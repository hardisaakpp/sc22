<?php

//$userId = $_POST["userId"];
$idcab = $_GET["idcab"];



include_once "bd_StoreControl.php";

$sentencia = $db->prepare("
    
delete from [dbo].[users]
            where id=?
        ");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute([$idcab]);

$result = $sentencia->rowCount();

//echo $result ;

header("Location: ../userL.php");
/*
//$row = $result->fetch_assoc();
$query = $sentencia->fetchAll(PDO::FETCH_OBJ);


if (count($query) > 0) { 
    $s1 = $db->prepare("UPDATE users SET password = ? WHERE  id = ? ;");
    $s1->execute([$newPass,$userId]);


    echo 1;
} else { //si no existe 
    echo 0;
}
*/