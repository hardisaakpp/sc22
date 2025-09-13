<?php
include_once "php/bd_StoreControl.php";

if (!isset($_GET["idcab"])) {
    exit("Falta parámetro idcab.");
}
$idcab  = intval($_GET["idcab"]);

$sql = "
SELECT 
    c.[fk_docnumsotcab] as DocNum,
    d.[LineNum],
    d.[ItemCode],
    d.[Quantity],
    c.[Filler] as FromWhsCod,
    c.[ToWhsCode] as WhsCode,
    d.[DocEntry_Sot] as BaseEntry,
    1250000001 as BaseType,
    d.[LineNum] as BaseLine
FROM [STORECONTROL].[dbo].[ced_groupsot] c
JOIN [dbo].[ced_groupsotdet] d 
    ON c.fk_docnumsotcab = d.DocNum_Sot 
    AND c.fk_idgroup = d.fk_idgroup
WHERE c.fk_idgroup = :idcab
";

$stmt = $db->prepare($sql);
$stmt->execute([":idcab"=>$idcab]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    exit("No hay datos para exportar.");
}

// Cabeceras para Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Grupo_".$idcab.".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Encabezados
echo implode("\t", array_keys($rows[0])) . "\n";

// Filas
foreach ($rows as $row) {
    echo implode("\t", $row) . "\n";
}
exit;
