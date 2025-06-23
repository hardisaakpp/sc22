<?php

require_once 'vendor/autoload.php';
require ('f_consumo.php');
require ('dat_cws.php');

date_default_timezone_set('America/Bogota');

//parametros
    //fecha
        //$fechaActual = date('d-m-Y');
        $fechaActualOriginal = $_POST['pFecha'];
        $fecAz =  explode("-",$fechaActualOriginal);
        $fecha = $fecAz[2].'-'.$fecAz[1].'-'.$fecAz[0];
    //almacen para cierre
       // $almacen =$_SESSION["whsCica"] ;// no del header xq puede actualizar un  administrador
        $tiendaCica= $_POST['tiendaTuremp'];
    //caja
        //hay q recorrer almenos 7 cajas, actualmente llegan a 6
    
    //codigos Hitell
            $sentencia2 = $db->query("select * from almacen where id=".$tiendaCica."  "  );
            $TEMP1 = $sentencia2->fetchObject();
        $h_cod_neg = $TEMP1->hit_cod_neg;;
        $h_cod_local = $TEMP1->hit_cod_local;

//proceso
    //encerar formas de pago
        for ($i=1; $i < 8; $i++) { 
            $sentencia1 = $db->prepare("exec sp_cicH_clearCaja  ?, ?, ?;" );
            $resultado1 = $sentencia1->execute([$tiendaCica, $fecha, $i]);
        }
    //registros de arqueo de caja
        //importo de hitell
        for ($i=1; $i < 8; $i++) { 

            $data2=consumo_arqueo($_SESSION['token'],$h_cod_neg,$h_cod_local,$i,$fecha);

            
            foreach ($data2 as $key => $value) {
                echo "Método de pago: " . $key . "\n";
                echo "Monto total: " . $value["total_amount"] . "\n";
                echo "Cantidad: " . $value["count"] . "\n";
                echo "\n";

                $sentencia1 = $db->prepare("exec sp_cicH_insertLine  ?, ?, ?, ?,?, ?;" );
                $resultado1 = $sentencia1->execute([$tiendaCica, $fecha, $i, $key, $value["total_amount"], $value["count"]]);

            }
    
            

        }




    
            
          



//echo "<a href='cic.php' class='btn btn-warning'>OK, VOLVER</a>";

//header("Location: cic.php");

?>

<?php include_once "footer.php" ?>