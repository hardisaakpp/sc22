<?php


$contraseña = "Datos.22";
$usuario = "consultas";
$nombreBaseDeDatos = "TimeSoft2";
$rutaServidor = "10.10.100.12";

try {
    $dbB = new PDO("sqlsrv:server=$rutaServidor;TrustServerCertificate=TRUE;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $dbB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos DB BIOMETRICOS TIMESOFT:  " . $e->getMessage();
}