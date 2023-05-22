<?php include_once "header.php" ?>
<?php
	
	if (!isset($_POST["mes"]) and !isset($_POST["year"]) )
	{
		$mes=date("m");
		$year=date("Y");
	}else
	{
		$mes=$_POST['mes'];
		$year=$_POST['year'];
	}
    

	$sentencia1 = $db->query("SELECT * FROM users WHERE id = '" . $userId . "'");
	$IDCONTEO = $sentencia1->fetchObject();
	$centrocosto = $IDCONTEO->Timesoft_CentroCosto;   


   $sentencia2 = $dbB->query("
   select * from (
SELECT *,(
	select top 1 descanso
		from(
			SELECT --LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida) as ini,
			concat( LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida) , ' -> ',TurnoHoraSalida,' [',(DATEDIFF(MINUTE, 
				LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida)
				, TurnoHoraSalida)),' minutos]') as descanso
			,DATEDIFF(MINUTE, LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida) , TurnoHoraSalida) AS minutos
			FROM (select 
					m.intidempleadoterminal, CAST(m.dtmFecha AS DATE) as fecha,
					convert(char(5), m.dtmFecha, 108) as TurnoHoraSalida
				from tblMarcaciones M) MC
			WHERE  intidempleadoterminal=QCABECERA.intIdEmpleadoTerminal 
				AND fecha=QCABECERA.fecha
		) laps
		where minutos is not null
				AND minutos > QCABECERA.intMinutosDescanso-30
				and minutos < QCABECERA.intMinutosDescanso+30
) AS DESCANSO 
,(
	select top 1 minutos
		from(
			SELECT 
				DATEDIFF(MINUTE, 
				LAG(TurnoHoraSalida, 1, NULL) OVER (order by TurnoHoraSalida)
				, TurnoHoraSalida)
				 AS minutos
			FROM (select 
					m.intidempleadoterminal, CAST(m.dtmFecha AS DATE) as fecha,
					convert(char(5), m.dtmFecha, 108) as TurnoHoraSalida
				from tblMarcaciones M) MC
			WHERE  intidempleadoterminal=QCABECERA.intIdEmpleadoTerminal 
				AND fecha=QCABECERA.fecha
		) laps
		where minutos is not null
				AND minutos > QCABECERA.intMinutosDescanso-30
				and minutos < QCABECERA.intMinutosDescanso+30 
) AS MINDESCANSO
FROM (select 
TP.intIdCentroCosto, TP.intIdEmpleadoTerminal,TP.strNombres , TP.strApellidos, TP.fecha, TP.intIdDetalleTurno, marcaciones 
,Q3.strNombre , Q3.intMinutosDescanso
,Q3.TurnoHoraEntrada,MC.horaentrada
,DATEDIFF(MINUTE, MC.horaentrada, Q3.TurnoHoraEntrada) AS [Entrada]
,Q3.TurnoHoraSalida, MC.horasalida 
,DATEDIFF(MINUTE, Q3.TurnoHoraSalida, MC.horasalida) AS [Salida]

from (select 
	uu.intIdCentroCosto, uu.intIdEmpleadoTerminal, uu.strNombres , uu.strApellidos,
	CAST(zU.dtmFechaInicio AS DATE) as fecha, intIdDetalleTurno  
	from tblTurnosProgramados zU 
	join tblEmpleados uu on zU.intIdEmpleado=uu.intIdEmpleado) TP

JOIN 
( select d.intiddetalleturno  , d.intMinutosDescanso ,
		convert(char(5), d.dtmHoraDesde, 108) as TurnoHoraEntrada,
		convert(char(5), d.dtmHoraHasta, 108) as TurnoHoraSalida,
		concat(t.strNombre,' ',  convert(char(5), d.dtmHoraDesde, 108) +  ' a ' + convert(char(5), d.dtmHoraHasta, 108)) as strNombre
	from tblTurnos t
		inner join tblDetalleTurnos d on t.intIdTurno = d.intIdTurno ) Q3 ON TP.intIdDetalleTurno=Q3.intIdDetalleTurno

LEFT join (select 	m.intidempleadoterminal, CAST(m.dtmFecha AS DATE) as fecha, 	count(convert(char(5), m.dtmFecha, 108)) as marcaciones
				,convert(char(5), min(m.dtmFecha), 108) as horaentrada,convert(char(5), max(m.dtmFecha), 108) as horasalida
		from tblMarcaciones M
		group by m.intidempleadoterminal, CAST(m.dtmFecha AS DATE)
	) MC 
on TP.intIdEmpleadoTerminal=MC.intIdEmpleadoTerminal and TP.fecha=MC.fecha) QCABECERA

	

WHERE intIdCentroCosto=".$centrocosto." 
AND month(fecha)=".$mes." and year(fecha)=".$year." 

) plus
where
((TurnoHoraEntrada<>TurnoHoraSalida) and (Entrada>30 or Salida>30)) or ((TurnoHoraEntrada=TurnoHoraSalida) and (marcaciones>0)) 
");
    $mascotas = $sentencia2->fetchAll(PDO::FETCH_OBJ);

?>


<form id="monthformX" method="post" action="" name="headbuscar">
    <div class="input-group">
    <div class="input-group-prepend">
        <span class="input-group-text" id="">Mes y año</span>
    </div>
        <select name="mes" id="mes" class="form-control">
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>

        <input type=number id="year" class="form-control" name="year" value=<?php echo $year; ?> >
        <input value="<?php echo $whsTurem; ?>" type="hidden" name="tiendaTuremp" id="tiendaTuremp" readonly="readonly"  class="form-control">
        <input type="submit" name="find" value="Buscar 🔎" class="form-control"
		onclick=this.form.action="turmar.php">
		<input type="submit" name="find" value="Descargar .xlsx" class="form-control"
		onclick=this.form.action="turEmpXlsx.php">
	
        </div>


	<div class="input-group">
		<div class="input-group-prepend">
			<span class="input-group-text" id="">Descripción de colores : </span>
		</div>
		<input type="submit" style="background-color: #FFEA8E" value="Ingreso o salida superior a -5 minutos" class="form-control" onclick=this.form.action="turmar1.php">
		<input type="submit" style="background-color: #D5FF8E" value="Trabajo mas de 30 minutos adicionales" class="form-control" onclick=this.form.action="turmar2.php">
		<input type="submit" style="background-color: #FF7373" value="Sin registro" class="form-control" onclick=this.form.action="turmar3.php">

	</div>

</form>








<script>
    document.getElementById('mes').value = <?php echo $mes; ?>;

	


</script>

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->


<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title"><?php echo $mes."/".$year ; ?> - Horarios generados:</strong>
        </div>
        <div class="card-body">



<!-- Aquí pon las col-x necesarias, comienza tu contenido, etcétera -->
	<div class="col-12">
		 <h2 style="color:gray";><?php echo $mes."/".$year ; ?> - Horarios generados:</h2>

<?php 
	if (count($mascotas)>0) {
?>

			<table class="table table-hover" name='tblList'>
				<thead class="thead-dark">
					<tr>
						<th>TERMINAL</th>	
						<th>NOMBRE</th>
						<th>FECHA</th>

						<th>TURNO</th>
						<!--<th>DESCANSO</th>-->
						<th>MARCACIONES</th>
						<th>ENTRADA</th>
						<th>SALIDA</th>
						<th>DESCANSO</th>
						
						<th> </th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($mascotas as $mascota){ ?>
						<tr>
						<td><?php echo $mascota->intIdEmpleadoTerminal ?></td>
						<td><?php echo $mascota->strNombres.' '. $mascota->strApellidos ?></td>
						<td><?php echo $mascota->fecha ?></td>

						<td><?php echo $mascota->strNombre ?></td>
						<!--<td><?php echo $mascota->intMinutosDescanso.' min.' ?></td>-->
						<!--MARCACIONES-->
							<?php
								if ($mascota->TurnoHoraEntrada == $mascota->TurnoHoraSalida && $mascota->marcaciones>0 ) {
									echo '<td style="background-color: #D5FF8E">'.$mascota->marcaciones.'</td>';
								} elseif ($mascota->TurnoHoraEntrada == $mascota->TurnoHoraSalida ) {
									echo '<td>'.$mascota->marcaciones.'</td>';
								} elseif ($mascota->marcaciones == NULL ) {
									echo '<td style="background-color: #FF7373 ">'.$mascota->marcaciones.'</td>';
								} elseif ($mascota->marcaciones <> 4 ) {
									echo '<td style="background-color: #FFEA8E ">'.$mascota->marcaciones.'</td>';
								} else {
									echo '<td>'.$mascota->marcaciones.'</td>';
								}
							?>

		
						<!--//HORA DE ENTRADA-->
							<?php
								if ($mascota->TurnoHoraEntrada == $mascota->TurnoHoraSalida ) {
									echo '<td>'.$mascota->horaentrada.'</td>';
								} elseif ($mascota->Entrada == NULL ) {
									echo '<td style="background-color: #FF7373 ">'.$mascota->horaentrada.'</td>';
								} elseif ($mascota->Entrada < -5) {
									echo '<td style="background-color:#FFEA8E">'.$mascota->horaentrada.'</td>';
								} elseif ($mascota->Entrada < 30) {
									echo '<td>'.$mascota->horaentrada.'</td>';
								} else {
									echo '<td  style="background-color: #D5FF8E ">'.$mascota->horaentrada.'</td>';
								}
							?>
						<!--//HORA DE SALIDA-->
							<?php
								if ($mascota->TurnoHoraEntrada == $mascota->TurnoHoraSalida ) {
									echo '<td>'.$mascota->horasalida.'</td>';
								} elseif ($mascota->Salida == NULL ) {
									echo '<td style="background-color: #FF7373 ">'.$mascota->horasalida.'</td>';
								} elseif ($mascota->Salida < -5) {
									echo '<td style="background-color:#FFEA8E">'.$mascota->horasalida.'</td>';
								} elseif ($mascota->Salida < 30) {
									echo '<td>'.$mascota->horasalida.'</td>';
								} else {
									echo '<td  style="background-color: #D5FF8E ">'.$mascota->horasalida.'</td>';
								}
							?>
						<!--//HORA DE SALIDA-->
							<?php
								if ($mascota->TurnoHoraEntrada == $mascota->TurnoHoraSalida ) {
									echo '<td>'.$mascota->DESCANSO.'</td>';
								} elseif ($mascota->MINDESCANSO == NULL ) {
									echo '<td style="background-color: #FF7373 ">'.$mascota->DESCANSO.'</td>';
								} elseif ($mascota->MINDESCANSO > $mascota->intMinutosDescanso) {
									echo '<td style="background-color:#FFEA8E">'.$mascota->DESCANSO.'</td>';
								} else {
									echo '<td>'.$mascota->DESCANSO.'</td>';
								}
							?>
					
							
							<td>
							<?php
								if ($mascota->marcaciones <> NULL ) {
									?>
									 <input type="button" onclick="window.open('turmarX.php?ter=<?php echo $mascota->intIdEmpleadoTerminal; ?>&fec=<?php echo $mascota->fecha; ?>',
            '_blank', 'width=400, height=200');" class="btn btn-secondary" name="atach" value="👆">
									<?php
								} 
							?>

                               

								
							</td>
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
		</div>
    </div>
</div>


<?php 

                    }
include_once "footer.php" ?>