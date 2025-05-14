<?php

$username = $_POST["username"];


include_once "bd_StoreControl.php";

$sentencia = $db->prepare("SELECT * FROM users WHERE username = ?;");
//$sentencia->bind_param('is', $userId, $oldPass); 
$sentencia->execute([$username]);

//$result = $sentencia->get_result();

//$row = $result->fetch_assoc();
$query = $sentencia->fetchAll(PDO::FETCH_OBJ);






    if (count($query) > 0) { 
       
    
    
        echo 1;
    } else { //si no existe 
        echo 0;
    }
    