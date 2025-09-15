<?php
header('Content-Type: application/json');
error_reporting(0); // evita warnings que rompan JSON

include_once "bd_StoreControl.php";

$idcab = intval($_GET['idcab'] ?? 0);
if (!$idcab) {
    echo json_encode(["error" => "Falta idcab"]);
    exit;
}

$stmt = $db->prepare("
    SELECT c.fk_docnumsotcab, c.Filler, c.ToWhsCode,
           d.ItemCode, d.Quantity, d.LineNum, d.DocEntry_Sot as BaseEntry
    FROM ced_groupsot c
    JOIN ced_groupsotdet d 
      ON c.fk_docnumsotcab = d.DocNum_Sot AND c.fk_idgroup = d.fk_idgroup
    WHERE c.fk_idgroup = ?
");
$stmt->execute([$idcab]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
