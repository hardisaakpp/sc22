<?php
include_once "php/bd_StoreControl.php";

$idcab = $_POST['idcab'] ?? null;
$towhs = $_POST['towhs'] ?? null;
$itemcode = $_POST['itemcode'] ?? null;
$quantity = $_POST['quantity'] ?? null;
$comment = $_POST['comment'] ?? null;

if ($idcab && $towhs && $itemcode && isset($quantity)) {
    // Guardar cantidad
    $stmt = $db->prepare("EXEC sp_InsertOrUpdateRepDet ?, ?, ?, ?");
    $stmt->execute([$idcab, $towhs, $itemcode, $quantity]);
    echo "OK";
} elseif ($idcab && $towhs && $itemcode && isset($comment)) {
    // Guardar comentario
    $stmt = $db->prepare("UPDATE [STORECONTROL].[dbo].[rep_det] SET [comment]=? WHERE [fk_id_cab]=? AND [ItemCode]=?");
    $stmt->execute([$comment, $idcab, $itemcode]);
    echo "OK";
} else {
    http_response_code(400);
    echo "Datos incompletos";
}
?>
