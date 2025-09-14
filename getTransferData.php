<?php
include_once "bd_StoreControl.php";

if (!isset($_GET['idcab'])) exit("Falta idcab");

$idcab = intval($_GET['idcab']);

$stmt = $db->prepare("
SELECT 
    c.fk_docnumsotcab,
    'sc22' as comments,
    c.Filler as fromWarehouse,
    c.ToWhsCode as toWarehouse,
    -2 as PriceList,
    d.ItemCode,
    d.Quantity,
    d.LineNum,
    c.ToWhsCode as warehouseCode,
    d.DocEntry_Sot as BaseEntry,
    d.LineNum as BaseLine,
    1250000001 as BaseType
FROM ced_groupsot c
JOIN ced_groupsotdet d 
    ON c.fk_docnumsotcab=d.DocNum_Sot 
   AND c.fk_idgroup=d.fk_idgroup
WHERE c.fk_idgroup=?
ORDER BY c.fk_docnumsotcab, d.LineNum
");
$stmt->execute([$idcab]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por fk_docnumsotcab
$transfers = [];
foreach ($rows as $row) {
    $doc = $row['fk_docnumsotcab'];
    if (!isset($transfers[$doc])) {
        $transfers[$doc] = [
            'cardCode' => '',
            'comments' => $row['comments'],
            'fromWarehouse' => $row['fromWarehouse'],
            'toWarehouse' => $row['toWarehouse'],
            'priceList' => $row['PriceList'],
            'stockTransferLines' => []
        ];
    }
    $transfers[$doc]['stockTransferLines'][] = [
        'itemCode' => $row['ItemCode'],
        'quantity' => (float)$row['Quantity'],
        'warehouseCode' => $row['warehouseCode'],
        'baseEntry' => (int)$row['BaseEntry'],
        'baseLine' => (int)$row['BaseLine'],
        'baseType' => (int)$row['BaseType']
    ];
}

header('Content-Type: application/json');
echo json_encode($transfers);
