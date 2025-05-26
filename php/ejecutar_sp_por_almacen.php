<?php
include_once "bd_StoreControl.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$whsCica = $data['whsCica'];
$fecha = $data['fecha'];

$resultados = [];

try {
    $procedimientos = [
        ["EXEC sp_cic_sincSAPSingle_45D ?, ?", "Sincronización SAP"],
        ["EXEC sp_cic_createCajas ?, ?", "Creación de cajas"],
        ["EXEC sp_cicUs_create ?, ?", "Carga de datos US"]
    ];

    foreach ($procedimientos as [$sql, $descripcion]) {
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$whsCica, $fecha]);
            $resultados[] = [
                "mensaje" => "✔️ [$whsCica] $descripcion completado",
                "exito" => true
            ];
        } catch (Exception $e) {
            $resultados[] = [
                "mensaje" => "❌ [$whsCica] Error en $descripcion: " . $e->getMessage(),
                "exito" => false
            ];
        }
    }

} catch (PDOException $e) {
    $resultados[] = ["mensaje" => "❌ Error general: " . $e->getMessage(), "exito" => false];
}

echo json_encode($resultados);
