<?php
include_once "php/bd_StoreControl.php";

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-d', strtotime('-7 days'));
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=resumen_depositos.xls");
header("Pragma: no-cache");
header("Expires: 0");

$sql = "
SELECT q1.fecha, q1.whsCode, q1.Efectivo, q2.Efectivo AS Depositado,
       (q1.Efectivo - ISNULL(q2.Efectivo, 0)) AS Diferencia,
       q2.PendienteSAP
FROM (
    SELECT c.fecha, c.whsCode, SUM(c.valRec) AS Efectivo
    FROM cicUs c
    JOIN Almacen a ON c.whsCode = a.cod_almacen
    WHERE c.fecha BETWEEN ? AND ? AND
          a.fk_emp = 'MT' AND
          (c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%'
           OR c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%')
    GROUP BY c.fecha, c.whsCode
) q1
LEFT JOIN (
    SELECT U_Fecha, U_WhsCode, SUM(TotalLC) AS Efectivo, 
           SUM(CASE WHEN creadoSAP = 0 THEN 1 ELSE 0 END) AS PendienteSAP
    FROM DepositosTiendas d
    WHERE d.U_Fecha BETWEEN ? AND ?
    GROUP BY d.U_Fecha, d.U_WhsCode
) q2 ON q1.whsCode = q2.U_WhsCode AND q1.fecha = q2.U_Fecha
";

$stmt = $db->prepare($sql);
$stmt->execute([$fechaInicio, $fechaFin, $fechaInicio, $fechaFin]);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);

// Imprimir encabezados
echo "Fecha\tWhsCode\tEfectivo (Cierre)\tDepositado\tDiferencia\tPor Integrar\n";

// Imprimir datos
foreach ($resumen as $r) {
    echo "{$r->fecha}\t{$r->whsCode}\t" .
         number_format($r->Efectivo, 2) . "\t" .
         number_format($r->Depositado ?? 0, 2) . "\t" .
         number_format($r->Diferencia, 2) . "\t" .
         ($r->PendienteSAP ?? 0) . "\n";
}
?>
