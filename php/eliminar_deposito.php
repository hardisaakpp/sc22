<?php
// eliminar_deposito.php

if (!isset($_GET['id']) || !isset($_GET['U_Fecha']) || !isset($_GET['U_WhsCode'])) {
    exit('Parámetros incompletos');
}

include_once "bd_StoreControl.php";

$id = (int)$_GET['id'];
$fecha = $_GET['U_Fecha'];
$whsCode = $_GET['U_WhsCode'];

// Ejecutar la eliminación
$sql = "DELETE FROM DepositosTiendas WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);

// Redirigir de vuelta
header("Location: ../depD.php?fecha=" . urlencode($fecha) . "&whsCode=" . urlencode($whsCode));
exit;
