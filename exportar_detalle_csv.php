<?php
include_once "php/bd_StoreControl.php";

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-d', strtotime('-7 days'));
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');

// Encabezados para forzar descarga del CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=detalle_depositos.csv');

// BOM para que Excel reconozca UTF-8 correctamente
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Cabecera del archivo CSV
fputcsv($output, [
    'Fecha Depósito',
    'Cuenta',
    'Monto Depositado',
    'Fecha Cierre',
    'Almacén',
    'Número Depósito',
    'Responsable',
    'Creado en SAP'
]);

// Consulta SQL corregida (la que tú usaste)
$sql = "
    SELECT TOP (1000) 
        d.DepositDate AS FechaDeposito, 
        c.AcctName, 
        d.TotalLC,
        d.U_Fecha AS FechaCierre, 
        d.U_WhsCode, 
        d.U_Ref_Bancar AS NumeroDeposito,
        d.Responsable, 
        d.creadoSAP
    FROM DepositosTiendas d
    JOIN CuentaFinanciera c ON d.DepositAccount = c.AcctCode
    WHERE d.U_Fecha BETWEEN ? AND ?
    ORDER BY d.U_Fecha DESC
";

$stmt = $db->prepare($sql);
$stmt->execute([$fechaInicio, $fechaFin]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['FechaDeposito'],
        $row['AcctName'],
        number_format($row['TotalLC'], 2),
        $row['FechaCierre'],
        $row['U_WhsCode'],
        $row['NumeroDeposito'],
        $row['Responsable'],
        $row['creadoSAP'] ? 'Sí' : 'No'
    ]);
}

fclose($output);
exit;
