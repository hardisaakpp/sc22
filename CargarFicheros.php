<?php

// Cómo subir el archivo
$fichero = $_FILES["file"];
$id = $_POST["id"];

// Cargando el fichero en la carpeta "subidas"
move_uploaded_file($fichero["tmp_name"], "films/".$id."/". $fichero["name"]);

// Redirigiendo hacia atrás
header("Location: " . $_SERVER["HTTP_REFERER"]);
?>