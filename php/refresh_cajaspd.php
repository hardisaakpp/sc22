<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$fecha = $data['fecha'];

// Simulación de consulta para obtener whsCica
$whsCica = obtenerWhsCica(); // Puedes reemplazar esta función con tu lógica real

$resultados = [];

try {
    $conn = new PDO("sqlsrv:Server=localhost;Database=STORECONTROL", "usuario", "contraseña");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $procedimientos = [
        "EXEC sp_cic_sincSAPSingle_45D '$whsCica', '$fecha'" => "Sincronización SAP",
        "EXEC sp_cic_createCajas '$whsCica', '$fecha'" => "Creación de cajas",
        "EXEC sp_cicUs_create '$whsCica', '$fecha'" => "Carga de datos US"
    ];

    foreach ($procedimientos as $sql => $descripcion) {
        try {
            $conn->exec($sql);
            $resultados[] = ["mensaje" => "✔️ $descripcion completado", "exito" => true];
        } catch (Exception $e) {
            $resultados[] = ["mensaje" => "❌ Error en $descripcion: " . $e->getMessage(), "exito" => false];
        }
    }

} catch (Exception $e) {
    $resultados[] = ["mensaje" => "❌ Error de conexión: " . $e->getMessage(), "exito" => false];
}

echo json_encode($resultados);

// Simulación de función para obtener whsCica
function obtenerWhsCica() {
    return 123; // Reemplaza con lógica real
}
