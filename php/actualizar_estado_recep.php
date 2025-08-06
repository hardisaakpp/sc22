<?php
include_once "bd_StoreControl.php"; // Asegúrate de que este archivo contiene la conexión $db

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idcab = $_POST['idcab'] ?? '';


    if (empty($idcab) ) {
        echo json_encode(["status" => "error", "message" => "Faltan parámetros."]);
        exit;
    }

    try {
            $stmt = $db->prepare("UPDATE [TransferenciasCabecera] set FechaIntegracion=GETDATE(), CreadaTransferencia=1 where id= = ?");
            $stmt->execute([$idcab]);

       

        echo json_encode(["status" => "success", "message" => "Estado actualizado correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>
