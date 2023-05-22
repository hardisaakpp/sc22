<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Pagina para control de inventarios">
    <meta name="author" content="Alex.Toasa@outlook.com">
    <title>Inventario|22</title>

    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <!-- Cargar el CSS de Boostrap-->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!--<script type="text/javascript" src="js/calendario.js"></script>-->
    <script src="js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="js/jquery-3.2.1.js" type="text/javascript"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

  </head>

<body>
  <?php
    // Validating Session
    session_start();
    
    //include_once "php/base_de_datos.php";
    date_default_timezone_set('America/Bogota');
    
    if (!isset($_GET["ter"]))
    {
      exit();
    }else
    {
      $ter=$_GET['ter'];
	  $fec=$_GET['fec'];
    }
      
      require 'php/bd_Biometricos.php';
      include_once "php/bd_StoreControl.php";

      

    //dias festivos
              $sentencia6 = $dbB->query(" SELECT --LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida) as ini,
			  concat( LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida) , ' -> ',TurnoHoraSalida,' [',(DATEDIFF(MINUTE, 
				  LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida)
				  , TurnoHoraSalida)),' minutos]') as descanso
			  ,DATEDIFF(MINUTE, LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida) , TurnoHoraSalida) AS minutos
			  FROM (select 
					  m.intidempleadoterminal, CAST(m.dtmFecha AS DATE) as fecha,
					  convert(char(5), m.dtmFecha, 108) as TurnoHoraSalida
				  from tblMarcaciones M) MC
			  WHERE  intidempleadoterminal=".$ter." 
				  AND fecha='".$fec."' " );
              $fers = $sentencia6->fetchAll(PDO::FETCH_OBJ);?>

<!-----------------------------------------------------MARCACIONES------------------------------------------------------------->
<!----------------------------------------------------------------------------------------------------------------------------------------->
<h2> MARCACIONES  </h2>

<div class="table-sm">
			<table class="table table-hover" name='tblList'>
				<thead class="thead-dark">
					<tr>
						<th>MARCACIONES</th>	
						<th>MINUTOS</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($fers as $mascota){ ?>
						<tr>
						<td><?php echo $mascota->descanso ?></td>
						<td><?php echo $mascota->minutos ?></td>

						
								
				
						</tr>
					<?php } ?>
				</tbody>
			<!--	<tfoot>
				<tr>
					<td colspan="3">Totales</td>
					<td id='ttHTR'></td>
				</tr>
				</tfoot>
						-->
			</table>
		</div>

<!-----------------------------------------------------TABLA JUSTIFICACIONES------------------------------------------------------------->





<?php

 //CONSULTO BODEGAS PARA CARGAR LA TABLA
 $sentencia2 = $dbB->query("
        
 select Q1.*, concat(Q2.strNombres,' ', Q2.strApellidos) as Nombre, 
 Q2.strIdentificacion , Q2.intIdEmpleadoTerminal
 ,Q3.strNombre
 ,convert(char(8), Q1.dtmEntrada, 108) as HoraEntrada 
 ,convert(char(8), Q1.dtmSalida, 108) as HoraSalida
 --,TurnoHoraEntrada,TurnoHoraSalida
 ,DATEDIFF(MINUTE, convert(char(8), Q1.dtmEntrada, 108) , TurnoHoraEntrada) AS [Entrada]
 ,DATEDIFF(MINUTE, TurnoHoraSalida, convert(char(8), Q1.dtmSalida, 108)) AS [Salida]
 from
 (
	 select     
					   case 
				 when intIdEstado=18 then T1.intIdTurno
				 else isnull(T2.intIdDetalleTurno,209)
			 end  AS intIdTurno,
			 T1.dtmEntrada, T1.dtmSalida,
			 case 
				when T1.dtmEntrada is null then CAST(T2.dtmFechaInicio AS DATE)
				else CAST(T1.dtmEntrada AS DATE)
			end as fecha,
 
		 case 
				 when T1.intIdEmpleado is null then T2.intIdEmpleado 
				 else T1.intIdEmpleado 
			 end as intIdEmpleado
 
		 
			 ,case 
				 when T1.intIdCentroCosto is null then T2.intIdCentroCosto 
				 else T1.intIdCentroCosto 
			 end as centrocosto
			 ,
			 ISNULL(T1.intIdEntradaSalidaEmpleado,0) AS intIdEntradaSalidaEmpleado,T1.strJustificacion
			 
 from
	 (select e.intIdEmpleadoTerminal ,m.intIdEntradaSalidaEmpleado, e.strIdentificacion 
		 ,m.intIdTurno, 
		 CONCAT(e.strNombres,' ',E.strApellidos) AS Nombre,
		 dtmEntrada, dtmSalida,tt.strNombre, m.strJustificacion
		 ,e.intIdEmpleado, m.intIdEstado, e.intIdCentroCosto
	 from [dbo].[tblEntradasSalidasEmpleados] m
		 JOIN [dbo].[tblEmpleados] e ON m.intIdEmpleado=e.intIdEmpleado
		 join ( select d.intiddetalleturno  , 
		 concat(t.strNombre,' ',  convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108)) as strNombre
		   from tblTurnos t
		   inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno ) tt
		   on m.intIdTurno=tt.intIdDetalleTurno
		   ) T1
 full outer JOIN (select zU.*,uu.intIdCentroCosto from tblTurnosProgramados zU join tblEmpleados uu on zU.intIdEmpleado=uu.intIdEmpleado) T2
	 ON T1.intIdEmpleado=T2.intIdEmpleado 
	 and CAST(T1.dtmEntrada AS DATE)=CAST(T2.dtmFechaInicio AS DATE)
 ) Q1
 JOIN tblEmpleados Q2 ON Q1.intIdEmpleado=Q2.intIdEmpleado
 JOIN 
 ( select d.intiddetalleturno  , 
		 convert(char(5), d.dtmHoraDesde, 108) as TurnoHoraEntrada, convert(char(5), d.dtmHoraHasta, 108) as TurnoHoraSalida,
		 concat(t.strNombre,' ',  convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108)) as strNombre
	 from tblTurnos t
		 inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno ) Q3 ON Q1.intIdTurno=Q3.intIdDetalleTurno
	 where dtmEntrada between '".$fec."' and  DATEADD(day, 1, '".$fec."') 
		 and  intidempleadoterminal=".$ter."
	 order by nombre,fecha " );
		 $regs = $sentencia2->fetchAll(PDO::FETCH_OBJ);

?>



<h2> JUSTIFICACIONES  </h2>

<table class="table" id="table-2">
    <thead class="thead-dark">
        <tr>
            <!--<th>ALMACEN</th>
            <th style="visibility:collapse; display:none;"></th>-->
            <th>FECHA</th>
            <th>TERMINAL</th>
            <th>CEDULA</th>
            <th>NOMBRE</th>
            <th>ENTRADA</th>
            <th>SALIDA</th>
            <th>TURNO</th>
            <th>JUSTIFICACION</th>

        </tr>
    </thead>
	<tbody>
    <?php foreach($regs as $reg){ ?>
        <tr>
            <td><?php echo $reg->fecha ?></td>
            <td><?php echo $reg->intIdEmpleadoTerminal ?></td>
            <td><?php echo $reg->strIdentificacion ?></td>
            <td><?php echo $reg->Nombre ?></td>
            <?php 
                //HORA DE ENTRADA
                    if ($reg->Entrada < -90 ) {
                        echo '<td style="background-color: #ffeeee ">'.$reg->HoraEntrada.'</td>';
                    } elseif ($reg->Entrada < -5) {
                        echo '<td style="background-color:#ffc0c0">'.$reg->HoraEntrada.'</td>';
                    } elseif ($reg->Entrada < 30) {
                        echo '<td>'.$reg->HoraEntrada.'</td>';
                    } elseif ($reg->Entrada < 90) {
                        echo '<td style="background-color:#c5ffbd">'.$reg->HoraEntrada.'</td>';
                    } else {
                        echo '<td  style="background-color: #eeffeb ">'.$reg->HoraEntrada.'</td>';
                    }
                //HORA DE SALIDA


                    if ($reg->HoraSalida == '') {
                        echo '<td style="background-color:salmon">'.$reg->HoraSalida.'</td>';
                    } else {
                        if ($reg->Salida < -90 ) {
                            echo '<td style="background-color: #ffeeee ">'.$reg->HoraSalida.'</td>';
                        } elseif ($reg->Salida < -5) {
                            echo '<td style="background-color:#ffc0c0">'.$reg->HoraSalida.'</td>';
                        } elseif ($reg->Salida < 30) {
                            echo '<td>'.$reg->HoraSalida.'</td>';
                        } elseif ($reg->Salida < 90) {
                            echo '<td style="background-color:#c5ffbd">'.$reg->HoraSalida.'</td>';
                        } else {
                            echo '<td  style="background-color: #eeffeb ">'.$reg->HoraSalida.'</td>';
                        }


                }
           ?>

            <td>                         
                <select name='turno' style='width:200px;' class='js-example-basic-single js-states form-control'  Size='Number_of_options' id="<?php echo 'tur'.$reg->intIdEntradaSalidaEmpleado ?>"  >
                    <option value=<?php echo $reg->intIdTurno  ?>> <?php echo $reg->strNombre ?>   </option>    
                    <?php foreach($turnos as $turno){ ?>
                        <option value=<?php echo $turno->intiddetalleturno ?>> <?php echo $turno->strNombre. ' '. $turno->Horario  ?>   </option>
                    <?php } ?>
                </select>
            </td>

             <td>                         
                <select name='turno' style='width:200px;' class='js-example-basic-single js-states form-control' name="<?php echo 'jus'.$reg->intIdEntradaSalidaEmpleado ?>"  id="<?php echo 'jus'.$reg->intIdEntradaSalidaEmpleado ?>"  Size='Number_of_options' >
                    <option value=<?php echo $reg->strJustificacion  ?>> <?php echo $reg->strJustificacion ?>   </option>    
                    <?php foreach($justifs as $justif){ ?>
                        <option value=<?php echo $justif->des ?>> <?php echo $justif->des ?>   </option>
                    <?php } ?>
                </select>
            </td>

        </tr>
    <?php } ?>
	</tbody>
</table>




<!----------------------------------------------------------------------------------------------------------------------------------------->








    <script src="js/bootstrap.bundle.min.js"></script>

      <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
  </body>
</html>                   