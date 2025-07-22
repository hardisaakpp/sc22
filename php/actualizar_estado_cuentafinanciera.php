<?php
include_once "bd_StoreControl.php"; // Asegúrate de que esta conexión sea la correcta

$acctCode = $_POST["acctCode"];
$estado = $_POST["estado"];

$s1 = $db->prepare("
    UPDATE CuentaFinanciera 
    SET DepositTiendas = ? 
    WHERE AcctCode = ?
");

$ok = $s1->execute([$estado, $acctCode]);

if ($ok) {
    echo "Estado actualizado correctamente.";
} else {
    echo "Error al actualizar el estado.";
}
?>
