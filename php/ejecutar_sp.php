<?php
include_once "bd_StoreControl.php";
header('Content-Type: application/json');

$sp = $_GET['sp'] ?? '';

if ($sp === 'sp_articuloF5') {
    try {
        $sql = "EXEC sp_articuloF5";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        echo json_encode([
            "success" => true,
            "mensaje" => "✔️ Procedimiento sp_articuloF5 ejecutado correctamente"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "error" => "❌ Error al ejecutar sp_articuloF5: " . $e->getMessage()
        ]);
    }
} else if ($sp === 'sp_sot_merge') {
    try {
        $sql = "EXEC sp_sot_merge";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        echo json_encode([
            "success" => true,
            "mensaje" => "✔️ Procedimiento sp_sot_merge ejecutado correctamente"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "error" => "❌ Error al ejecutar sp_sot_merge: " . $e->getMessage()
        ]);
    }
} else if ($sp === 'sp_almacenF5') {
    try {
        $sql = "EXEC sp_almacenF5";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        echo json_encode([
            "success" => true,
            "mensaje" => "✔️ Procedimiento sp_almacenF5 ejecutado correctamente"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "error" => "❌ Error al ejecutar sp_almacenF5: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "error" => "❌ Procedimiento no permitido o no especificado"
    ]);
}
?>
