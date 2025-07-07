<?php

//$userId = $_POST["userId"];
$Username = $_POST["Username"];
$password = $_POST["password"];
if (isset($_POST["conteo"])) {
    $conteo = $_POST["conteo"];
}else{
    $conteo = 0;
}
$items = $_POST["items"];
$whsCierre = $_POST["whsCierre"];
$whsInvs = $_POST["whsInvs"];
$whsBodega = $_POST["whsBodega"];
$whsTransitorio = $_POST["whsTransitorio"];
//$codTimeSoft = $_POST["codTimeSoft"];
$perfil = $_POST["radios"];
$email1 = $_POST["Email1"];
$email2 = $_POST["Email2"];

/*echo 
$email1 .
$email2 ;
*/


include_once "bd_StoreControl.php";

$sentencia = $db->prepare("
    INSERT INTO [dbo].[users]
            ([username]
            ,[password]
            ,[articulosContar]
            ,[realizaConteo]
            ,[fk_ID_almacen_cierre]
            ,[fk_ID_almacen_invs]
            ,[fk_ID_almacen_bodeg]
            ,[fk_ID_almacen_transitorio]

            ,[perfil]
            ,[email]
            ,[emailSuper]
            )
    VALUES
            (?
            ,?
            ,?
            ,?
            ,?
            ,?
            ,?
            ,?
            
            ,?
            ,?
            ,?
        );");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute([$Username, $password, $items, $conteo,$whsCierre, $whsInvs, $whsBodega, $whsTransitorio,  $perfil ,$email1, $email2]);

$result = $sentencia->rowCount();

//echo $result ;

header("Location: ../userN.php?row=".$result);
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