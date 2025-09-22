<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);

include_once "bd_StoreControl.php";

// Recibir JSON desde fetch
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['idcab']) || !isset($data['codigos'])) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

$idcab = (int)$data['idcab'];
$codigos = array_unique($data['codigos']); // eliminar duplicados
$respuesta = [];

try {
    // Preparar el insert
    $stmtIns = $db->prepare("
        INSERT INTO TransferenciasDiferencias (id_TrCab, CodeBars, datetime, product) 
        VALUES (?, ?, GETDATE(), ?)
    ");

    // Preparar consulta para obtener producto
    $stmtProd = $db->prepare("
        SELECT TOP 1 CONCAT(ID_articulo, ' ', descripcion) AS producto 
        FROM Articulo 
        WHERE codigoBarras = ?
    ");

    foreach ($codigos as $c) {
        $producto = "❌ No encontrado"; // valor por defecto

        // Consultar producto por código de barras
        $stmtProd->execute([$c]);
        $row = $stmtProd->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['producto']) && $row['producto'] !== "") {
            $producto = $row['producto'];
        }

        // Insertar en la BD
        $stmtIns->execute([$idcab, $c, $producto]);

        // Obtener datetime real del insert
        $datetime = $db->query("SELECT TOP 1 datetime FROM TransferenciasDiferencias WHERE id_TrCab = $idcab AND CodeBars = '$c' ORDER BY datetime DESC")->fetchColumn();

        // Agregar a la respuesta
        $respuesta[] = [
            "CodeBars" => $c,
            "producto" => $producto,
            "datetime" => $datetime
        ];
    }

    echo json_encode($respuesta);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

exit();
