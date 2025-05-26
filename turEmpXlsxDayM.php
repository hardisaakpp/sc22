<?php
session_start();
$mes=$_POST['mes'];
		$year=$_POST['year'];

        require 'vendor/autoload.php';
        include_once "php/bd_StoreControl.php";

$sentencia2 = $db->query("
SELECT 
    fk_emp,[cod_almacen]
    ,[Expr1] as almacen,[cedula]
    ,[nombre]
    ,[apellido]
    ,FORMAT ([Date], 'dd/MM/yyyy') as fecha
    ,[Festivo]
    ,[des_turno]
    , dtmHoraDesde, dtmHoraHasta, intMinutosDescanso, minlab_turno
    ,[HREQUERIDAS]
    ,(HREQUERIDAS*60)-minlab_turno as  minDiferencia
FROM [STORECONTROL].[dbo].[vw_turnosEmp]
where month([Date])=".$mes." and year([Date])=".$year." " );
$regs = $sentencia2->fetchAll(PDO::FETCH_OBJ);


//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Alex Toasa")->setTitle("Turnos");

$spreadsheet->setActiveSheetIndex(0);
$hojaActiva = $spreadsheet->getActiveSheet();
//cabeceras
$hojaActiva->setCellValue('A1', 'empresa');
$hojaActiva->setCellValue('B1', 'local');
$hojaActiva->setCellValue('C1', 'fecha');
$hojaActiva->setCellValue('D1', 'cedula');
$hojaActiva->setCellValue('E1', 'nombre');
$hojaActiva->setCellValue('F1', 'hora entrada');
$hojaActiva->setCellValue('G1', 'hora salida');
$hojaActiva->setCellValue('H1', 'min.descanso');
$hojaActiva->setCellValue('I1', 'min.laborables');
//detalle

$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->fk_emp);
   $hojaActiva->setCellValue('B'.$count , $reg->cod_almacen.' '.$reg->almacen);
   $hojaActiva->setCellValue('C'.$count , $reg->fecha);
   $hojaActiva->setCellValue('D'.$count , $reg->cedula);
   $hojaActiva->setCellValue('E'.$count , $reg->nombre.' '.$reg->apellido);
   $hojaActiva->setCellValue('F'.$count , $reg->dtmHoraDesde);
   $hojaActiva->setCellValue('G'.$count , $reg->dtmHoraHasta);
   $hojaActiva->setCellValue('H'.$count , $reg->intMinutosDescanso);
   $hojaActiva->setCellValue('I'.$count , $reg->minlab_turno);
   
  /* $spreadsheet->getActiveSheet()->getStyle('B'.$count )
   ->getNumberFormat()
   ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
   $spreadsheet->getActiveSheet()->getStyle('C'.$count )
   ->getNumberFormat()
   ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
*/
/*
$date=$reg->fecha;
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
 $column='C';
 if ($date != '') {
     $t_date   = PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($date);
     if ($t_date !== false) {
        $hojaActiva->setCellValue('C'.$count , $t_date);
        $hojaActiva->getStyle('C'.$count )->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        $hojaActiva->getStyle('C'.$count )->getFont()->setBold(true);
        $hojaActiva->getStyle('C'.$count )->getFont()->setBold(false);
     }
  }
*/
   $count++;
}

/*
$hojaActiva->setCellValue('B2', 1222.22);

$hojaActiva->setCellValue('C1', 'SEGUIDO1')->setCellValue('D1','SEGUIDO 2');
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Horarios por Dia.xls"');
header('Cache-Control: max-age=0');

$writer = IOfactory::createWriter($spreadsheet,'Xlsx');
$writer->save('php://output');
 exit;
 ?>

 
