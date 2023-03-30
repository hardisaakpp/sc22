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
    



   $sentencia2 = $db->query("select * from turem where mes=".$mes." and anio=".$year." and fk_id_almacen=".$whsTurem." ");
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
		onclick=this.form.action="turEmpY.php">
		<input type="submit" name="find" value="Descargar .xlsx" class="form-control"
		onclick=this.form.action="turEmpXlsx.php">
		<input type="submit" name="add"  value="Agregar ➕" class="form-control"
		onclick=this.form.action="turEmpN.php">
        </div>
</form>

<script>
    document.getElementById('mes').value = <?php echo $mes; ?>;
</script>

<div class="row">



<!-- Aquí pon las col-x necesarias, comienza tu contenido, etcétera -->
	<div class="col-12">
		 <h2 style="color:gray";><?php echo $mes."/".$year ; ?> - Horarios generados:</h2>

<?php 
	if (count($mascotas)>0) {
?>
		<div class="table-sm">
			<table class="table table-hover">
				<thead class="thead-dark">
					<tr>
						<th>CEDULA</th>	
						<th>NOMBRE</th>
						<th>APELLIDO</th>
						<th> </th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($mascotas as $mascota){ ?>
						<tr>
							<td><?php echo $mascota->cedula ?></td>
							<td><?php echo $mascota->nombre ?></td>
							<td><?php echo $mascota->apellido ?></td>
							<td>


                                <input type="button" onclick="window.open('turEmpX.php?id=<?php echo $mascota->id; ?>',
            '_blank', 'width=1100, height=700');" class="btn btn-secondary" name="atach" value="Editar 📝">

								<a class="btn btn-warning" href="<?php echo "turempDel.php?id=" . $mascota->id?>">Eliminar ⛔</a>			
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
 
	
		






<?php 

                    }
include_once "footer.php" ?>