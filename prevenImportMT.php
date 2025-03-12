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
            $arrayANIO= array();
            $arrayMES= array();
            $arrayCODE= array();
       
 
            for ($i=1; $i<count($sheetData); $i++) {
                
                $ianio = $sheetData[$i][0]; 
                $imes =  $sheetData[$i][1]; 
                $icod =  $sheetData[$i][2]; 
           
                
                array_push($arrayANIO, $ianio);
                array_push($arrayMES, $imes);
                array_push($arrayCODE, $icod);
             
            };
            $distinctANIO = array_unique($arrayANIO);
            $distinctMES = array_unique($arrayMES);
            $distinctCODE = array_unique($arrayCODE);
 
            //var_dump($input);
            //print_r($distinctCEDS);
            //print_r($distinctTURNOS);
            //print_r($arrayFECHAS);
           // print_r($arrayCEDS);
            
           
           //validaciones etapa 1 formatos
            foreach ($distinctANIO as $i => $value) {
                if (!is_numeric($distinctANIO[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, EL AÑO DEBE SER NUMERICO <br>';
                    $errors++;
                }                
                elseif (empty($distinctANIO[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, AÑO VACIO <br>';
                    $errors++;
                }elseif ($distinctANIO[$i]<>2025) {
                    echo 'Registro ' . $i+2 . ' - ERROR, EL AÑO ESTA FUERA DEL RANGO PERMITIDO (2025) <br>';
                    $errors++;
                }
            }

            for ($i=1; $i<count($sheetData); $i++) {
                
                $ILOC = $sheetData[$i][4]; 
                if (empty($ILOC)) {
                    echo 'Registro ' . $i+2 . ' - ERROR, LOCAL VACIO <br>';
                    $errors++;
                }
             
            };


            foreach ($distinctMES as $i => $value) {
                if (!is_numeric($distinctMES[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, EL MES DEBE SER NUMERICO <br>';
                    $errors++;
                }                
                elseif (empty($distinctMES[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, MES VACIO <br>';
                    $errors++;
                }elseif ($distinctMES[$i]<>3) {
                    echo 'Registro ' . $i+2 . ' - ERROR, EL VALOR MES ESTA FUERA DEL RANGO PERMITIDO (MARZO) <br>';
                    $errors++;
                }
            }

            foreach ($distinctCODE as $i => $value) {
                if (empty($distinctCODE[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, CODIGO EMPLEADO VACIA <br>';
                    $errors++;
                }elseif (!is_numeric($distinctCODE[$i])) {
                    echo 'Registro ' . $i+2 . ' - ERROR, EL CODIGO DEBE SER NUMERICO <br>';
                    $errors++;
                }
            }



            //validaciones etapa 2 datos en base
            if ($errors==0)  {
                ////VALIDO EXISTAN EN BASE DE DATOS
                foreach ($distinctCODE as $i => $value) {
                    $sentencia3 = $db->query("
                    select count(*) as ROWS from Vendedores_OSLP where SlpCode=".trim($distinctCODE[$i])." and fk_emp='MT'
                    " );
                    $regC = $sentencia3->fetchObject();
                            $XROWS=$regC->ROWS;
                        // echo $XROWS;
                    if ($XROWS==0) {
                        echo 'Registro ' . $i+2 . ' - ERROR, CODIGO NO EXISTE EN BASE DE EMPLEADOS <br>';
                        $errors++;
                    }
                }
              
            }

          

        
            if ($errors>0) {
                echo '<h1> ❌ CORRIJA LOS ERRORES Y VUELVA A INTENTAR </h1>
                <a href="prevenImpRL.php" class="btn btn-warning">OK, VOLVER</a>';
            } else {
                echo '<h3> 👍 Validacion de Archivo </h3>';


                //------------------------------------------------------------
         
                   for ($i=1; $i<count($sheetData); $i++) {

                        $zanio = $sheetData[$i][0];
                        $zmes = $sheetData[$i][1];
                        $zcod = $sheetData[$i][2];
                        $zvalor = $sheetData[$i][3];
                        $zlocal = $sheetData[$i][4];

                        $tenciaq3 = $db->prepare("select id from Vendedores_OSLP
                        where fk_emp='MT' and SlpCode=".$zcod." " );
                          $tenciaq3->execute();
                        $asas = $tenciaq3->fetchObject();
                                $zcode_id=$asas->id;

                        $sentenc1 = $db->prepare("
                            
                            declare @mes int;
                                set @mes=".$zmes.";
                            declare @anio int;
                                set @anio=".$zanio.";
                            declare @fk_id_empleados int;
                                set @fk_id_empleados=".$zcode_id.";
                            declare @valor float;
                                set @valor=".$zvalor.";

                            declare @local nvarchar(30);
                                set @local='".$zlocal."';
                            IF EXISTS(
                            SELECT * FROM VendMetas
                            WHERE anio = @anio AND mes = @mes AND fk_id_empleados=@fk_id_empleados and  whsCode=@local
                            )
                                UPDATE VendMetas 
                                    SET meta = @valor
                                    WHERE anio = @anio AND mes = @mes AND fk_id_empleados=@fk_id_empleados and  whsCode=@local;
                            ELSE
                            INSERT VendMetas (fk_id_empleados,mes,anio,meta, whsCode) values (@fk_id_empleados,@mes,@anio,@valor,@local);
                            " );
                    $sentenc1->execute();
                       

                    };

                    echo '<h3> 👍 Datos insertados correctamente </h3>
              
                    <a href="prevenL.php" class="btn btn-warning">OK, VOLVER</a>';



            } 
        }
    }
};
?>

<?php include_once "footer.php" ?>