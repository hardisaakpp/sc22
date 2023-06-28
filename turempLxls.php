<?php
session_start();


        require 'vendor/autoload.php';
        include_once "php/bd_Biometricos.php";


        $ti=$_GET['ti'];
        $idcab=$_GET['idcab'];


if ($idcab=='MT') {
  $sentencia2 = $dbB->query("
      select d.intiddetalleturno
      ,
        convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Horario
      ,t.strNombre, intMinutosDescanso as minDescanso,
      case
      when dtmHoraDesde>dtmHoraHasta then
        DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso 
      else 
      DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso 
      end
      AS minTrabajo

        from tblTurnos t
        inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
      where 
      t.intIdTurno in (4,11,12,14,17,15,16) 
      " );
  $regs = $sentencia2->fetchAll(PDO::FETCH_OBJ);
} else if ($idcab=='CE'){
  $sentencia2 = $dbB->query(" select d.intiddetalleturno,
            convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Horario
            ,t.strNombre, intMinutosDescanso as minDescanso,
            case
            when dtmHoraDesde>dtmHoraHasta then
                DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso 
            else 
                DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso 
                end
            AS minTrabajo

            from tblTurnos t
            inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
            where 
            t.intIdTurno in (6,11,12,14,17,15,16) " );
            $regs = $sentencia2->fetchAll(PDO::FETCH_OBJ); 
}else{
  $sentencia2 = $dbB->query(" select d.intiddetalleturno,
  convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Horario
  ,t.strNombre, intMinutosDescanso as minDescanso,
  case
  when dtmHoraDesde>dtmHoraHasta then
      DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso 
  else 
      DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso 
      end
  AS minTrabajo

  from tblTurnos t
  inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
  where 
  t.intIdTurno in (5,6,11,12,14,17,15,16,3,4,11,12,14,17,15,16) " );
  $regs = $sentencia2->fetchAll(PDO::FETCH_OBJ); 
}





//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\{SpreadSheet, IOfactory};
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Alex Toasa")->setTitle("Turnos");

$spreadsheet->setActiveSheetIndex(0);
$hojaActiva = $spreadsheet->getActiveSheet();
//cabeceras
$hojaActiva->setCellValue('A1', 'CODIGO');
$hojaActiva->setCellValue('B1', 'HORARIO');
$hojaActiva->setCellValue('C1', 'NOMBRE');
$hojaActiva->setCellValue('D1', 'MIN.DESCANSO');
$hojaActiva->setCellValue('E1', 'MIN.TRABAJO');
//detalle


$count = 2;
foreach ($regs as $reg) {
   // echo $reg->identificacion.' '.$reg->fecha ;
   $hojaActiva->setCellValue('A'.$count , $reg->intiddetalleturno);
   $hojaActiva->setCellValue('B'.$count , $reg->Horario);
   $hojaActiva->setCellValue('C'.$count , $reg->strNombre);
   $hojaActiva->setCellValue('D'.$count , $reg->minDescanso);
   $hojaActiva->setCellValue('E'.$count , $reg->minTrabajo);
   

   $count++;
}

/*
$hojaActiva->setCellValue('B2', 1222.22);

$hojaActiva->setCellValue('C1', 'SEGUIDO1')->setCellValue('D1','SEGUIDO 2');
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="MaestroHorarios.xls"');
header('Cache-Control: max-age=0');

$writer = IOfactory::createWriter($spreadsheet,'Xls');
$writer->save('php://output');
 exit;
 ?>

 