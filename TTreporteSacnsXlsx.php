<?php
session_start();

		

        require 'vendor/autoload.php';
        include_once "php/bd_StoreControl.php";


        if (!isset($_GET["idcab"])) {
            exit();
        }
        $idcab = $_GET["idcab"];
        $ti = $_GET["ti"];
        $s1 = $db->query("exec sp_getTFT_resumen ".$idcab.",".$ti." " );
        $regs = $s1->fetchAll(PDO::FETCH_OBJ);   

//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Alex Toasa")->setTitle("Reporte".$idcab);

$spreadsheet->setActiveSheetIndex(0);
$hojaActiva = $spreadsheet->getActiveSheet();
//cabeceras
$hojaActiva->setCellValue('A1', 'codigoBarras');
$hojaActiva->setCellValue('B1', 'ID_articulo');
$hojaActiva->setCellValue('C1', 'descripcion');
$hojaActiva->setCellValue('D1', 'stock');
$hojaActiva->setCellValue('E1', 'scans');
$hojaActiva->setCellValue('F1', 'diferencia');
$hojaActiva->setCellValue('G1', 'categoria');
$hojaActiva->setCellValue('H1', 'subcategoria');
$hojaActiva->setCellValue('I1', 'nombreGrupo');
$hojaActiva->setCellValue('J1', 'familia');

//detalle

$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->codigoBarras);
   $hojaActiva->setCellValue('B'.$count , $reg->ID_articulo);
   $hojaActiva->setCellValue('C'.$count , $reg->descripcion);
   $hojaActiva->setCellValue('D'.$count , $reg->stock);
   $hojaActiva->setCellValue('E'.$count , $reg->scans);
   $hojaActiva->setCellValue('F'.$count , $reg->scans-$reg->stock);
   $hojaActiva->setCellValue('G'.$count , $reg->categoria);
   $hojaActiva->setCellValue('H'.$count , $reg->subcategoria);
   $hojaActiva->setCellValue('I'.$count , $reg->nombreGrupo);
   $hojaActiva->setCellValue('J'.$count , $reg->familia);
   

   $count++;
}

/*
$hojaActiva->setCellValue('B2', 1222.22);

$hojaActiva->setCellValue('C1', 'SEGUIDO1')->setCellValue('D1','SEGUIDO 2');
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="librodescarga.xls"');
header('Cache-Control: max-age=0');

$writer = IOfactory::createWriter($spreadsheet,'Xls');
$writer->save('php://output');
 exit;
 ?>

 