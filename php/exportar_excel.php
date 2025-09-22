<?php
require_once "../vendor/autoload.php"; // PhpSpreadsheet, ajústalo según tu ruta
include_once "bd_StoreControl.php"; // tu conexión PDO

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-2 days'));
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$spreadsheet = new Spreadsheet();

// ================= HOJA 1 =================
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle("Cabecera");

// Query cabecera
$sqlCab = "
    SELECT c.[DocDate], c.[DocNum], c.[Filler], c.[ToWhsCode],
           c.[LineStatus], c.[Comments], c.[DocStatus],
           c.[Series], c.[Hour]
    FROM [dbo].[SotCab_MT] c
    WHERE c.DocDate BETWEEN :desde AND :hasta
";
$stmt = $db->prepare($sqlCab);
$stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
$cabRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Escribir cabecera
if ($cabRows) {
    $sheet1->fromArray(array_keys($cabRows[0]), NULL, 'A1');
    $sheet1->fromArray($cabRows, NULL, 'A2');
}

// ================= HOJA 2 =================
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle("Detalle");

// Query detalle
$sqlDet = "
    SELECT d.[ItemCode], d.[Quantity], d.[DocNum_Sot], d.[DocEntry_Sot],
           d.[Dscription], d.[CodeBars], d.[LineNum]
    FROM [dbo].[SotDet_MT] d
    JOIN [SotCab_MT] c ON d.DocNum_Sot = c.DocNum
    WHERE c.DocDate BETWEEN :desde AND :hasta
";
$stmt = $db->prepare($sqlDet);
$stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
$detRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Escribir detalle
if ($detRows) {
    $sheet2->fromArray(array_keys($detRows[0]), NULL, 'A1');
    $sheet2->fromArray($detRows, NULL, 'A2');
}

// ================= Descargar Excel =================
$filename = "SOT_Export_" . date('Ymd_His') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
