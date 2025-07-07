<?php

// Cómo subir el archivo
$fichero = $_FILES["file"];
$id = $_POST["id"];

// Cargando el fichero en la carpeta "subidas"
$destino = "films/" . $id . "/";
if (!is_dir($destino)) {
    mkdir($destino, 0777, true);
}
if (!is_writable($destino)) {
    chmod($destino, 0777);
}
move_uploaded_file($fichero["tmp_name"], $destino . $fichero["name"]);

// Redirigiendo hacia atrás
header("Location: " . $_SERVER["HTTP_REFERER"]);
?>