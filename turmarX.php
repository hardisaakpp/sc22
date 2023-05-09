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

<!-----------------------------------------------------CALENDARIO MES------------------------------------------------------------->
<!----------------------------------------------------------------------------------------------------------------------------------------->
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




    <script src="js/bootstrap.bundle.min.js"></script>

      <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
  </body>
</html>                   