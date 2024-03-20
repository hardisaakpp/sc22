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
				where month([Date])=".$mes." and year([Date])=".$year." ");
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
		onclick=this.form.action="turEmpY3.php">
		<input type="submit" name="find" value="Descargar .xlsx" class="form-control"
		onclick=this.form.action="turEmpXlsxDayM.php">

        </div>
</form>

<script>
    document.getElementById('mes').value = <?php echo $mes; ?>;

	


</script>

<div class="row">



<!-- Aquí pon las col-x necesarias, comienza tu contenido, etcétera -->
	<div class="col-12">


<?php 
	if (count($mascotas)>0) {
?>
	<div class="card">
        <div class="card-header">
            <strong class="card-title"><?php echo $mes."/".$year ; ?> - Horarios generados por dia: </strong>
        </div>
        <div class="card-body">
			<table id="bootstrap-data-table" class="table table-striped table-bordered" name='tblList'>
				<thead class="thead-dark">
					<tr>
						<th>EMP</th>	
						<th>COD</th>
						<th>ALMACEN</th>
						<th>CEDULA</th>
						<th>NOMBRE</th>
						

						<th>DIA</th>
						<th>HORARIO</th>
						<th>MIN. DESCANSO</th>
						<th>MIN. LABORADOS</th>
						<th>DIFERENCIA</th>
						
					</tr>
				</thead>
				<tbody>
					<?php foreach($mascotas as $mascota){ ?>
						<tr>
							<td><?php echo $mascota->fk_emp ?></td>
							<td><?php echo $mascota->cod_almacen ?></td>
							<td><?php echo $mascota->almacen ?></td>
							<td><?php echo $mascota->cedula ?></td>
							<td><?php echo $mascota->nombre.' '. $mascota->apellido ?></td>

							<td><?php echo $mascota->fecha ?></td>
							<td><?php echo $mascota->des_turno ?></td>
							<td><?php echo $mascota->intMinutosDescanso ?></td>
							<td><?php echo $mascota->minlab_turno ?></td>
							<td><?php echo $mascota->minDiferencia ?></td>

							
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

<?php 

                    }
include_once "footer.php" ?>