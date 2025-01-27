<?php
//include 'conexion.php';

// Crear nuevo depósito
function crearDeposito($conn, $fecha, $cantidad, $descripcion) {
    $sql = "INSERT INTO depositos (fecha, cantidad, descripcion) VALUES ('$fecha', '$cantidad', '$descripcion')";
    $conn->query($sql);
}

// Eliminar depósito
function eliminarDeposito($conn, $id) {
    $sql = "DELETE FROM depositos WHERE id=$id";
    $conn->query($sql);
}

// Actualizar depósito
function actualizarDeposito($conn, $id, $fecha, $cantidad, $descripcion) {
    $sql = "UPDATE depositos SET fecha='$fecha', cantidad='$cantidad', descripcion='$descripcion' WHERE id=$id";
    $conn->query($sql);
}

// Filtrar por fecha
function obtenerDepositos($conn, $fecha_inicio = null, $fecha_fin = null) {
    $where = "";
    if ($fecha_inicio && $fecha_fin) {
        $where = "WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    return $conn->query("SELECT * FROM CiCaDep $where order by fecha_cica desc");
}
?>
