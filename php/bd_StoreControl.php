<?php
$contraseña = "Datos.22";
$usuario = "consultas";
$nombreBaseDeDatos = "STORECONTROL";
$rutaServidor = "10.10.100.12";
try {
    $db = new PDO("sqlsrv:server=$rutaServidor;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos: " . $e->getMessage();
}