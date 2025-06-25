<?php
include_once "bd_StoreControl.php"; // Asegúrate de que este archivo contiene la conexión $db

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fk_docnumsotcab = $_POST['fk_docnumsotcab'] ?? '';
    $fk_idgroup = $_POST['fk_idgroup'] ?? '';

    if (empty($fk_docnumsotcab) || empty($fk_idgroup)) {
        echo json_encode([
            "status" => "error",
            "message" => "Faltan parámetros: fk_docnumsotcab y/o fk_idgroup."
        ]);
        exit;
    }

    try {
        $stmt = $db->prepare("EXEC sp_actualiza_ced_groupsotdet @fk_idgroup = :fk_idgroup, @DocNum_Sot = :docnum");
        $stmt->bindParam(':fk_idgroup', $fk_idgroup, PDO::PARAM_INT);
        $stmt->bindParam(':docnum', $fk_docnumsotcab, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode([
            "status" => "success",
            "message" => "Solicitud actualizada correctamente."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Método no permitido."
    ]);
}
?>
