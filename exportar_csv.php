<?php
include_once "php/bd_StoreControl.php";

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-d', strtotime('-7 days'));
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');
$marcaSeleccionada = $_GET["marca"] ?? "Todos";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=resumen_depositos.csv');
$output = fopen('php://output', 'w');

// Escribir cabecera
fputcsv($output, ['Fecha', 'WhsCode', 'Efectivo (Cierre)', 'Depositado', 'Diferencia', 'PendienteSAP']);

// Consulta
$sql = "
SELECT q1.fecha, q1.whsCode, q1.Efectivo, q2.Efectivo AS Depositado,
       (q1.Efectivo - ISNULL(q2.Efectivo, 0)) AS Diferencia,
       q2.PendienteSAP
FROM (
    SELECT c.fecha, c.whsCode, SUM(c.valRec) AS Efectivo
    FROM cicUs c
    JOIN Almacen a ON c.whsCode = a.cod_almacen
    WHERE c.fecha BETWEEN :f1 AND :f2
      AND a.fk_emp = 'MT'
      AND (
            c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%'
         OR c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%'
      )
";

$params = [
    ":f1" => $fechaInicio,
    ":f2" => $fechaFin,
    ":f3" => $fechaInicio,
    ":f4" => $fechaFin
];

if ($marcaSeleccionada !== "Todos") {
    $sql .= " AND ISNULL(a.[marca], '') = :marca ";
    $params[":marca"] = $marcaSeleccionada;
}

$sql .= "
    GROUP BY c.fecha, c.whsCode
) q1
LEFT JOIN (
    SELECT U_Fecha, U_WhsCode, SUM(TotalLC) AS Efectivo, 
           SUM(CASE WHEN creadoSAP = 0 THEN 1 ELSE 0 END) AS PendienteSAP
    FROM DepositosTiendas d
    WHERE d.U_Fecha BETWEEN :f3 AND :f4
    GROUP BY d.U_Fecha, d.U_WhsCode
) q2 ON q1.whsCode = q2.U_WhsCode AND q1.fecha = q2.U_Fecha
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Escribir datos
foreach ($rows as $row) {
    fputcsv($output, [
        $row['fecha'],
        $row['whsCode'],
        number_format($row['Efectivo'], 2),
        number_format($row['Depositado'] ?? 0, 2),
        number_format($row['Diferencia'], 2),
        $row['PendienteSAP'] ?? 0
    ]);
}
fclose($output);
exit;
?>
