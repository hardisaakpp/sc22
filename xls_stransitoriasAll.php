<?php
session_start();

        require 'vendor/autoload.php';
        include_once "php/bd_StoreControl.php";



    $sentencia2 = $db->query("exec sp_getStockTransitorioItemsAll" );
    $regs = $sentencia2->fetchAll(PDO::FETCH_OBJ);

//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\{SpreadSheet, IOfactory};
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Alex Toasa")->setTitle("Turnos");

$spreadsheet->setActiveSheetIndex(0);
$hojaActiva = $spreadsheet->getActiveSheet();
//cabeceras
$hojaActiva->setCellValue('A1', 'Destino');
$hojaActiva->setCellValue('B1', 'DocDate');
$hojaActiva->setCellValue('C1', 'DocNum');
$hojaActiva->setCellValue('D1', 'OpenQty');

//detalle


$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->ToWhsCode);
   //$hojaActiva->setCellValue('B'.$count , $reg->fecha);
   $hojaActiva->setCellValue('C'.$count , $reg->DocNum);
   $hojaActiva->setCellValue('D'.$count , $reg->OpenQty);
   
  /* $spreadsheet->getActiveSheet()->getStyle('B'.$count )
   ->getNumberFormat()
   ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
   $spreadsheet->getActiveSheet()->getStyle('C'.$count )
   ->getNumberFormat()
   ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
*/
$date=$reg->DocDate;
$column='B';
if ($date != '') {
    $t_date   = PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($date);
    if ($t_date !== false) {
       $hojaActiva->setCellValue('B'.$count , $t_date);
       $hojaActiva->getStyle('B'.$count )->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
       $hojaActiva->getStyle('B'.$count )->getFont()->setBold(true);
       $hojaActiva->getStyle('B'.$count )->getFont()->setBold(false);
    }
 }

   $count++;
}

/*
$hojaActiva->setCellValue('B2', 1222.22);

$hojaActiva->setCellValue('C1', 'SEGUIDO1')->setCellValue('D1','SEGUIDO 2');
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="NumTransfTransitorias.xls"');
header('Cache-Control: max-age=0');

$writer = IOfactory::createWriter($spreadsheet,'Xls');
$writer->save('php://output');
 exit;
 ?>

 