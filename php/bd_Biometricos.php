<?php
$contraseña = "ppMEkwDrem";
$usuario = "tswmabel";
//$nombreBaseDeDatos = "dbTimeSoftWebAutomatic_MABEL";  ///produccion
$nombreBaseDeDatos = "dbTimeSoftWebAutomatic_MABEL";
$rutaServidor = "34.68.56.120";
try {
    $dbB = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $dbB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos DB BIOMETRICOS TIMESOFT:  " . $e->getMessage();
}