<?php
session_start();
$mes=$_POST['mes'];
		$year=$_POST['year'];

        require 'vendor/autoload.php';
        include_once "php/bd_StoreControl.php";

$sentencia2 = $db->query("
select LTRIM(RTRIM(cedula)) as identificacion, 
FORMAT (f.Date, 'dd/MM/yyyy')  as fecha,
CASE
WHEN d.cod_turno=209 THEN 'LIBRE'
ELSE CAST(d.cod_turno AS nvarchar(10))
END as turno
from turem t
	join turem_day d on t.id=d.fk_id_turem
	join Dim_Fecha f on d.fk_DateKey=f.DateKey
where mes='".$mes."' and anio='".$year."' " );
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
$hojaActiva->setCellValue('A1', 'identificacion');
$hojaActiva->setCellValue('B1', 'fechaInicio');
$hojaActiva->setCellValue('C1', 'fechaFin');
$hojaActiva->setCellValue('D1', 'turno');
$hojaActiva->setCellValue('E1', 'ubicaciones');
//detalle


$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->identificacion);
   //$hojaActiva->setCellValue('B'.$count , $reg->fecha);
   //$hojaActiva->setCellValue('C'.$count , $reg->fecha);
   $hojaActiva->setCellValue('D'.$count , $reg->turno);
   
  /* $spreadsheet->getActiveSheet()->getStyle('B'.$count )
   ->getNumberFormat()
   ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
   $spreadsheet->getActiveSheet()->getStyle('C'.$count )
   ->getNumberFormat()
   ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
*/
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

   $count++;
}

/*
$hojaActiva->setCellValue('B2', 1222.22);

$hojaActiva->setCellValue('C1', 'SEGUIDO1')->setCellValue('D1','SEGUIDO 2');
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Horarios.xls"');
header('Cache-Control: max-age=0');

$writer = IOfactory::createWriter($spreadsheet,'Xlsx');
$writer->save('php://output');
 exit;
 ?>

 