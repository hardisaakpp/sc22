<?php
session_start();

		

        require 'vendor/autoload.php';
        include_once "php/bd_StoreControl.php";


        if (!isset($_GET["idcab"])) {
            exit();
        }
        $idcab = $_GET["idcab"];

        $s1 = $db->query("
        select c.fecScan, username, c.barcode, a.ID_articulo, a.descripcion, a.nombreGrupo
        from StockScan c
            join users u on c.id_user=u.id
            left join Articulo a on c.barcode=a.codigoBarras
        where fk_id_stockCab=".$idcab."
        order by 1 desc " );
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
$hojaActiva->setCellValue('A1', 'fecScan');
$hojaActiva->setCellValue('B1', 'username');
$hojaActiva->setCellValue('C1', 'barcode');
$hojaActiva->setCellValue('D1', 'ID_articulo');
$hojaActiva->setCellValue('E1', 'descripcion');
$hojaActiva->setCellValue('F1', 'nombreGrupo');




//detalle

$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->fecScan);
   $hojaActiva->setCellValue('B'.$count , $reg->username);
   $hojaActiva->getCell('C'.$count)
                ->setValueExplicit(
                $reg->barcode,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                );
   $hojaActiva->setCellValue('D'.$count , $reg->ID_articulo);
   $hojaActiva->setCellValue('E'.$count , $reg->descripcion);
   $hojaActiva->setCellValue('F'.$count , $reg->nombreGrupo);
   

   $count++;
}




/*
$hojaActiva->setCellValue('B2', 1222.22);

$hojaActiva->setCellValue('C1', 'SEGUIDO1')->setCellValue('D1','SEGUIDO 2');
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="LogScansUsers.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOfactory::createWriter($spreadsheet,'Xlsx');
$writer->save('php://output');
 exit;
 ?>

 