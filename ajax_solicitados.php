<?php
include_once "php/bd_StoreControl.php";
header('Content-Type: application/json');

$itemcode = $_POST['itemcode'] ?? null;
$towhs    = $_POST['towhs'] ?? null;

if (!$itemcode || !$towhs) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
    exit;
}

$sql = "
    SELECT SUM(d.Quantity) AS solicitados
    FROM [STORECONTROL].[dbo].[rep_cab] c
    JOIN [STORECONTROL].[dbo].[rep_det] d ON c.id = d.fk_id_cab
    WHERE CAST(c.fecCreacion AS date) = CAST(GETDATE() AS date)
      AND c.ToWhs <> ?
      AND d.ItemCode = ?
";

$stmt = $db->prepare($sql);
$stmt->execute([$towhs, $itemcode]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$solicitados = $result['solicitados'] ?? 0;

echo json_encode([
    'success' => true,
    'solicitados' => (int)$solicitados
]);
