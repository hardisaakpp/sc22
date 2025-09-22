<?php
include_once "bd_StoreControl.php"; // Asegúrate de que este archivo contiene la conexión $db

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fk_idgroup = $_POST['fk_idgroup'] ?? '';
    $docnum_sot = $_POST['docnum_sot'] ?? '';

    if (empty($fk_idgroup) || empty($docnum_sot)) {
        echo json_encode(["status" => "error", "message" => "Faltan parámetros."]);
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE ced_groupsot SET transferencia = 1,[enabled]=0 WHERE fk_idgroup = ? AND fk_docnumsotcab = ?");
        $stmt->execute([$fk_idgroup, $docnum_sot]);

        echo json_encode(["status" => "success", "message" => "Estado actualizado correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>
