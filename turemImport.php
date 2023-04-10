<?php
require_once 'vendor/autoload.php';

include_once "header.php";

//$conn = mysqli_connect("localhost","root","","mbsgn");
date_default_timezone_set('America/Bogota');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
 
$errors= 0;

function validar_fecha_espanol($fecha){
	$valores = explode('/', $fecha);
	if(count($valores) == 3){
       // echo $valores[0].$valores[1].$valores[2];
		if ($valores[0]<13 && $valores[0]>0 && checkdate($valores[0], $valores[1], $valores[2])) {
            return true;
        }else {
            return false;
        }
    }
	return false;
};


if (isset($_POST['submit'])) {
 
    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
     
    if(isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
     
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);
     
        if('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }
 
        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
 
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
         
        if (!empty($sheetData)) {
            $arrayCEDS= array();
            $arrayTURNOS= array();
            $arrayFECHAS= array();
 
            for ($i=1; $i<count($sheetData); $i++) {
                $identificacion = substr('0'.$sheetData[$i][0], -10);
                $fecha = $sheetData[$i][1];
                $turno = $sheetData[$i][3];

                /*$fecA =  explode("/",$fecha);

                $dia = $fecA[0];
                $mes = $fecA[1];
                $anio = $fecA[2];*/

                array_push($arrayCEDS, $identificacion);
                array_push($arrayTURNOS, $turno);
                array_push($arrayFECHAS, $fecha);
             
            };
            $distinctCEDS = array_unique($arrayCEDS);
            $distinctTURNOS = array_unique($arrayTURNOS);
            $distinctFECHAS = array_unique($arrayFECHAS);
 

           

            //var_dump($input);
            //print_r($distinctCEDS);
            //print_r($distinctTURNOS);
            //print_r($arrayFECHAS);
           // print_r($arrayCEDS);
            
           
           //validaciones etapa 1 formatos
            foreach ($distinctTURNOS as $i => $value) {
                if (empty($distinctTURNOS[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, CODIGO DE TURNO VACIO <br>';
                    $errors++;
                }elseif (!is_numeric($distinctTURNOS[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, EL CODIGO DE TURNO DEBE SER NUMERICO <br>';
                    $errors++;
                }
            }

            foreach ($distinctFECHAS as $i => $value) {
                
                if (empty($distinctFECHAS[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, FECHA VACIA <br>';
                    $errors++;
                }elseif (!validar_fecha_espanol($distinctFECHAS[$i])) {
                    echo 'Registro ' . $i+2 . ' - FECHA INVALIDA <br>';
                    $errors++;
                }
            }

            foreach ($distinctCEDS as $i => $value) {
                if (empty($distinctCEDS[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, IDENTIFICACION VACIA <br>';
                    $errors++;
                }
            }

            //validaciones etapa 2 datos en base
            if ($errors==0)  {
                ////VALIDO EXISTAN EN BASE DE DATOS
                foreach ($distinctCEDS as $i => $value) {
                    $sentencia3 = $dbB->query("select count(*) as ROWS from tblEmpleados e where e.strIdentificacion=RIGHT('0".trim($distinctCEDS[$i])."',10) " );
                    $regC = $sentencia3->fetchObject();
                            $XROWS=$regC->ROWS;
                        // echo $XROWS;
                    if ($XROWS==0) {
                        echo 'Registro ' . $i+2 . ' - ERROR, CEDULA NO EXISTE EN BASE DE EMPLEADOS <br>';
                        $errors++;
                    }
                }
                foreach ($distinctTURNOS as $i => $value) {
                    $sentencia3 = $dbB->query("SELECT count(*) as ROWS FROM tblDetalleTurnos WHERE intIdDetalleTurno='".trim($distinctTURNOS[$i])."' " );
                    $regC = $sentencia3->fetchObject();
                            $XROWS=$regC->ROWS;
                        // echo $XROWS;
                    if ($XROWS==0) {
                        echo 'Registro ' . $i+2 . ' - ERROR, CODIGO DE TURNO NO EXISTE EN BASE DE EMPLEADOS <br>';
                        $errors++;
                    }
                }



            }

          

        
            if ($errors>0) {
                echo '<h1> ❌ CORRIJA LOS ERRORES Y VUELVA A INTENTAR </h1>
                <a href="turempimp.php" class="btn btn-warning">OK, VOLVER</a>';
            } else {
                echo '<h3> 👍 Validacion de Archivo </h3>';

                //generar unicos empleado/mes ppara generar en mis tablas
                $arraybCEDFEC = array();
                for ($i=1; $i<count($sheetData); $i++) {
                    $identificacion =substr('0'.$sheetData[$i][0], -10);
                    $fecha = $sheetData[$i][1];

                    $fecA =  explode("/",$fecha);
    
                    $dia = $fecA[1];
                    $mes = $fecA[0];
                    $anio = $fecA[2];

                    array_push($arraybCEDFEC, array($identificacion,$mes,$anio));

                };
                $serialized = array_map('serialize', $arraybCEDFEC);
                $unique = array_unique($serialized);
                
                $distinctMesCedula =array_intersect_key($arraybCEDFEC, $unique);

               // var_dump($distinctMesCedula);

                //echo $_POST['tiendaTuremp'];

                //recorrer array distinct de Cabecera, si no existe creo
                foreach ($distinctMesCedula as $bike){
                    $cedula = $bike[0];
                    $mes = $bike[1];
                    $anio = $bike[2];
                    $sentencia4 = $dbB->query("select top 1 strNombres, strApellidos, intIdEmpleadoTerminal, monSalario  from tblEmpleados e where strIdentificacion= RIGHT('0".trim($cedula)."',10) " );

                    $regC = $sentencia4->fetchObject();
                            $xNombre=$regC->strNombres;
                            $xApellido=$regC->strApellidos;
                            $xTerminal=$regC->intIdEmpleadoTerminal;
                            $xSalario=$regC->monSalario;
                    $sentencia1 = $db->prepare("exec sp_newTurEmp  ?, ?, ?, ?,?, ?, ?, ? ;" );
                    $resultado1 = $sentencia1->execute([$_POST['tiendaTuremp'], $cedula, $xNombre, $xApellido, $mes, $anio, $xTerminal, $xSalario]);
                    
                   }

             

                   for ($i=1; $i<count($sheetData); $i++) {
                        $zidentificacion = substr('0'.$sheetData[$i][0], -10);
                        $zfecha = $sheetData[$i][1];
                        $zturno = $sheetData[$i][3];

                        $fecAz =  explode("/",$zfecha);
        
                        $tenciaq3 = $dbB->prepare(" select 
                        case
                        when dtmHoraDesde>dtmHoraHasta then
                        (DATEDIFF(MINUTE,   dtmHoraDesde, DATEADD(day,1,dtmHoraHasta))-intMinutosDescanso )/60
                        else 
                        (DATEDIFF(MINUTE,   dtmHoraDesde, dtmHoraHasta)-intMinutosDescanso )/60
                          end
                         AS Trab
                         , t.strNombre +  ' ' + convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108) as Descripcion
                            from tblTurnos t
                            inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno
                          where 
                          d.intiddetalleturno='".$zturno."' " );
                          $tenciaq3->execute();
                        $asas = $tenciaq3->fetchObject();
                                $XminTrab=$asas->Trab;
                                $xDescripcion=$asas->Descripcion;



                        $diaa = $fecAz[1];
                        $mesa = $fecAz[0];
                        $anioa = $fecAz[2];

                        $sentenc1 = $db->prepare("
                            declare @idalm int;
                            set @idalm=".$_POST['tiendaTuremp'].";
                            declare @mes int;
                            set @mes=".$mesa.";
                            declare @dia int;
                            set @dia=".$diaa.";
                            declare @anio int;
                            set @anio=".$anioa.";
                            declare @cedula nvarchar(13);
                            set @cedula='".$zidentificacion."';
                            declare @codTurno int;
                            set @codTurno=".$zturno.";
                            declare @minTrab decimal;
                            set @minTrab=CAST('".$XminTrab."' AS decimal);

                            declare @idTurem int;
                            set @idTurem=(select top 1 id as idTurem from turem where cedula like '%".$zidentificacion."%' and mes=@mes and anio=@anio and fk_id_almacen=@idalm);
                            declare @DateKey int;
                            set @DateKey= cast((CONCAT((right('00' + CAST( @anio as nvarchar(4)),4)),(right('00' + CAST( @mes as nvarchar(4)),2)),(right('00' + CAST( @dia as nvarchar(4)),2)))) as int);   

                            UPDATE [dbo].[turem_day]
                            SET [cod_turno] = @codTurno, horlab_turno=@minTrab, des_turno='".$xDescripcion."'
                            WHERE [fk_DateKey]= @DateKey and [fk_id_turem]=@idTurem 
                            " );
                    $sentenc1->execute();
                       

                    };

                    echo '<h3> 👍 Datos insertados correctamente </h3>
              
                    <a href="turempimp.php" class="btn btn-warning">OK, VOLVER</a>';



            } 
        }
    }
};
?>

<?php include_once "footer.php" ?>