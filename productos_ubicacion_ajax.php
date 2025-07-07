<?php
// productos_ubicacion_ajax.php
include_once "php/bd_StoreControl.php";
session_start();

header('Content-Type: application/json');

if (!isset($_GET['absEntry'])) {
    echo json_encode(['error' => 'Falta parámetro absEntry']);
    exit;
}
$absEntry = intval($_GET['absEntry']);

try {
    // Usa la variable $db de bd_StoreControl.php si existe, si no crea PDO
    $pdo = isset($db) ? $db : new PDO($dsn, $dbuser, $dbpass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare("EXEC sp_sap_ConsultarProductosUbicacion @AbsEntry = :absEntry");
    $stmt->bindParam(':absEntry', $absEntry, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
