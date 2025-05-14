<?php

$userId = $_POST["userId"];
$oldPass = $_POST["oldPass"];
$newPass = $_POST["newPass"];

include_once "bd_StoreControl.php";

$sentencia = $db->prepare("SELECT * FROM users WHERE id = ? and password = ?;");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute([$userId, $oldPass]);

//$result = $sentencia->get_result();

//$row = $result->fetch_assoc();
$query = $sentencia->fetchAll(PDO::FETCH_OBJ);


if (count($query) > 0) { 
    $s1 = $db->prepare("UPDATE users SET password = ? WHERE  id = ? ;");
    $s1->execute([$newPass,$userId]);


    echo 1;
} else { //si no existe 
    echo 0;
}
