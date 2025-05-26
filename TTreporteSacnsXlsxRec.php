<?php
session_start();

		

        require 'vendor/autoload.php';
        include_once "php/bd_StoreControl.php";


        if (!isset($_GET["idcab"])) {
            exit();
        }
        $idcab = $_GET["idcab"];
        $ti = $_GET["ti"];
        $s1 = $db->query("exec sp_getTFT_reCsumen ".$idcab." " );
        $regs = $s1->fetchAll(PDO::FETCH_OBJ);   

//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\SpreadSheet;
use PhpOffice\PhpSpreadsheet\IOfactory;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
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
$hojaActiva->setCellValue('E1', 'Conteo');
$hojaActiva->setCellValue('F1', 'Reconteo');
$hojaActiva->setCellValue('G1', 'diferencia');
$hojaActiva->setCellValue('H1', 'categoria');
$hojaActiva->setCellValue('I1', 'subcategoria');
$hojaActiva->setCellValue('J1', 'nombreGrupo');
$hojaActiva->setCellValue('K1', 'familia');

//detalle

$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->codigoBarras);
   $hojaActiva->setCellValue('B'.$count , $reg->ID_articulo);
   $hojaActiva->setCellValue('C'.$count , $reg->descripcion);
   $hojaActiva->setCellValue('D'.$count , $reg->stock);
   $hojaActiva->setCellValue('E'.$count , $reg->conteo);
   $hojaActiva->setCellValue('F'.$count , $reg->scans);
   $hojaActiva->setCellValue('G'.$count , $reg->scans-$reg->stock);
   $hojaActiva->setCellValue('H'.$count , $reg->categoria);
   $hojaActiva->setCellValue('I'.$count , $reg->subcategoria);
   $hojaActiva->setCellValue('J'.$count , $reg->nombreGrupo);
   $hojaActiva->setCellValue('K'.$count , $reg->familia);
   

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

 