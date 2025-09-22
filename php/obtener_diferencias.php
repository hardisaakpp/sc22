<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

include_once "bd_StoreControl.php";

if (!isset($_GET['idcab'])) {
    echo json_encode([]);
    exit();
}

$idcab = (int)$_GET['idcab'];

try {
    $stmt = $db->prepare("SELECT CodeBars, datetime, product 
                      FROM TransferenciasDiferencias 
                      WHERE id_TrCab = ? 
                      ORDER BY datetime ASC");

    $stmt->execute([$idcab]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($registros);

} catch (PDOException $e) {
    echo json_encode([]);
}

exit();
