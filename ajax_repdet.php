<?php
include_once "php/bd_StoreControl.php";

$idcab = $_POST['idcab'] ?? null;
$towhs = $_POST['towhs'] ?? null;
$itemcode = $_POST['itemcode'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if ($idcab && $towhs && $itemcode && isset($quantity)) {
    $stmt = $db->prepare("EXEC sp_InsertOrUpdateRepDet ?, ?, ?, ?");
    $stmt->execute([$idcab, $towhs, $itemcode, $quantity]);
    echo "OK";
} else {
    http_response_code(400);
    echo "Datos incompletos";
}
?>
