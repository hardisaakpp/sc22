<?php
if (!isset($_GET["id"])) {
    exit("ID no proporcionado");
}

$id = $_GET["id"];
include_once "bd_StoreControl.php"; // Asegúrate de que esta conexión sea válida

$sentencia = $db->prepare("DELETE FROM ced_groupCE WHERE id = ?");
$resultado = $sentencia->execute([$id]);

if ($resultado) {
    header("Location: ../cediGrpDCE.php"); // Redirige de nuevo a la página principal
} else {
    echo "Error al eliminar el grupo.";
}
?>
