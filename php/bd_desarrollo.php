<?php
$contraseña = 'M4B31$tr$d3s4rr0ll0*BDl0g1n#1*';
$usuario = "desadmin";
$nombreBaseDeDatos = "MODULOS_SC";
$rutaServidor = "192.168.2.211";
try {
    $dbdev = new PDO("sqlsrv:server=$rutaServidor;TrustServerCertificate=TRUE;database=$nombreBaseDeDatos", $usuario, $contraseña);
    $dbdev->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Ocurrió un error con la base de datos STORE CONTROL: " . $e->getMessage();
}