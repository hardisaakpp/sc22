<?php
include_once "bd_StoreControl.php";

$data = json_decode(file_get_contents('php://input'), true);
$fecha = $data['fecha'];
$empresa = $data['empresa']; // MT o CE

$resultados = [];

try {
    // Obtener todos los whsCica válidos según la empresa seleccionada
    $stmt = $db->prepare("
        SELECT id 
        FROM Almacen 
        WHERE hit_cod_local IS NOT NULL 
          AND hit_cod_local <> 0 
          AND fk_emp = ?
    ");
    $stmt->execute([$empresa]);
    $almacenes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($almacenes as $whsCica) {
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
    }

} catch (PDOException $e) {
    $resultados[] = ["mensaje" => "❌ Error general: " . $e->getMessage(), "exito" => false];
}

echo json_encode($resultados);
