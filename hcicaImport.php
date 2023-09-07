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



$fechaActualOriginal = $_POST['pFecha'];

$fecAz =  explode("-",$fechaActualOriginal);

$fechaActual = $fecAz[2].'-'.$fecAz[1].'-'.$fecAz[0];



//$fechaActual = date('d-m-Y');
$tiendaCica= $_POST['tiendaTuremp'];

//echo ('.'. $_FILES['file']['name']);

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($_FILES['file']['tmp_name']);
$sheetData = $spreadsheet->getActiveSheet()->toArray();


if (!empty($sheetData)) {
        
    $arrayCAJA= array();  
   
    for ($i=1; $i<count($sheetData); $i++) {
        $fecha = $sheetData[$i][0];
        $ingreso = (float) str_replace(",","",(str_replace("$","",$sheetData[$i][6]))) ;
        $mediocobro = $sheetData[$i][4];
        if ($fecha==$fechaActual && $ingreso>0 && $mediocobro!='Efectivo - Dotación inicial') {
            $caja = $sheetData[$i][2];
            $fecA =  explode("-",$fecha);
            $dia = $fecA[0];
            $mes = $fecA[1];
            $anio = $fecA[2];
            $fechaN = $fecA[2].'-'.$fecA[1].'-'.$fecA[0];
            array_push($arrayCAJA, $caja.'/'.$fechaN);
        } 
    };
    $distinctCAJA = array_unique($arrayCAJA);

    foreach ($distinctCAJA as $i => $value) {
       
        $parametros =  explode("/",$distinctCAJA[$i]);
        $icaja = $parametros[0];
        $ifec = $parametros[1];

        
    $sentencia1 = $db->prepare("exec sp_CiCaH_clearCaja  ?, ?, ?;" );
    $resultado1 = $sentencia1->execute([$tiendaCica, $ifec, $icaja]);

    }

    for ($i=1; $i<count($sheetData); $i++) {
            $fecha = $sheetData[$i][0];
            $caja = $sheetData[$i][2];
            $mediocobro = $sheetData[$i][4];
            $movimientos = $sheetData[$i][5];
            $ingreso = (float) str_replace(",","",(str_replace("$","",$sheetData[$i][6]))) ;


        if ($fecha==$fechaActual && $ingreso>0 && $mediocobro!='Efectivo - Dotación inicial') {

            $fecA =  explode("-",$fecha);

            $dia = $fecA[0];
            $mes = $fecA[1];
            $anio = $fecA[2];

            $fechaN = $fecA[2].'-'.$fecA[1].'-'.$fecA[0];

            $sentencia1 = $db->prepare("exec sp_CiCaH_insertLine  ?, ?, ?, ?,?, ?;" );
            $resultado1 = $sentencia1->execute([$tiendaCica, $fechaN, $caja, $mediocobro, $ingreso, $movimientos]);


            echo($mediocobro.$ingreso.' ✔️<br>');

        } else {
            echo($mediocobro.$ingreso.' ➖<br>'); 
           /* echo $fechaActual.'->'.$fecha;*/
          
        }     
    };
    

}

echo "<a href='hcica.php' class='btn btn-warning'>OK, VOLVER</a>";

//header("Location: hcica.php");

?>

<?php include_once "footer.php" ?>