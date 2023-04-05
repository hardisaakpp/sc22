<?php
    include_once "header.php";
	



	if (!isset($_POST["mes"]) and !isset($_POST["year"]) )
	{
		$mes=date("m");
		$year=date("Y");
	}else
	{
		$mes=$_POST['mes'];
		$year=$_POST['year'];
	}
    



$sentencia2 = $db->query("select  top 1 fk_ID_almacen_turemp,Timesoft_CentroCosto
from	users
where	username='". $userName ."' AND fk_ID_almacen_turemp<>0" );
$regCodCierre = $sentencia2->fetchObject();

$centrocosto = $regCodCierre->Timesoft_CentroCosto;




   $sentencia3 = $dbB->query("SELECT 
                strIdentificacion,strNombres,strApellidos,intIdEmpleadoTerminal, ISNULL(monSalario, 0)  as 'monSalario'
            FROM [dbTimeSoftWebAutomatic_MABEL].[dbo].[tblEmpleados]
            where intIdCentroCosto='". $centrocosto ."' " );
   $mascotas = $sentencia3->fetchAll(PDO::FETCH_OBJ);




	//echo(count($mascotas));
	//echo(count($reconteos));
?>
<form id="monthformX" method="post" action="">
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
        <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control"
		onclick=this.form.action="turEmpY.php">
		<input type="submit" name="add"  value="Agregar ➕" class="form-control"
		onclick=this.form.action="turEmpN.php">
        </div>
</form>


<script>
    document.getElementById('mes').value = <?php echo $mes; ?>;
</script>

<div class="row">

<script>

function indexesLX(){

try {
    $(".boton").click(function() {

        
    var cedula='';
    // Obtenemos todos los valores contenidos en los <td> de la fila
    // seleccionada
    $(this).parents("tr").find(".cedula").each(function() {
        cedula += $(this).html() + "\n";
    });

    var nombre='';
    $(this).parents("tr").find(".nombre").each(function() {
        nombre += $(this).html() + "\n";
    });

    var apellido='';
    $(this).parents("tr").find(".apellido").each(function() {
        apellido += $(this).html() + "\n";
    });

    var terminal='';
    $(this).parents("tr").find(".terminal").each(function() {
        terminal += $(this).html() + "\n";
    });

    var sueldo= 0.0;
    console.log(sueldo);
    $(this).parents("tr").find(".sueldo").each(function() {
        //sueldo += $(this).html() + "\n";
        sueldo = parseFloat($(this).html());
    });
    console.log(sueldo);
    /* console.log(valores);
    alert(valores);*/
    myFunction(cedula,nombre, apellido, terminal, sueldo);
    });
}
    catch(x) { /* puede usarse cualquier otro nombre en lugar de 'x' */
    //document.getElementById("ejemplo").innerHTML = x.message;
}

    
   // setInterval('contadoradd()',2000);
}

   
  </script>


<!-- Aquí pon las col-x necesarias, comienza tu contenido, etcétera -->







	<div class="col-12">
		 <h2 style="color:gray";><?php echo $mes."/".$year ; ?> - Nuevo Usuario</h2>



         <div class="col-12">


<div ALIGN="right" >

</div>    
    <div class="table-responsive">
        <table class="table table-bordered display" id="example" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <!--<th>FECHA STOCK</th>-->
            
                    <th>CEDULA</th>
                    <th>NOMBRES</th>
                    <th>APELLIDOS</th>
                    <th>CODIGO TERMINAL</th>
                    <th></th>
                    <th></th>
                 
                </tr>
            </thead>
            <tbody>
            <?php 
            foreach($mascotas as $mascota){   ?>
                    <tr>
                   
                        
                        <td class="cedula"><?php echo $mascota->strIdentificacion ?></td>
                        <td class="nombre"><?php echo $mascota->strNombres ?></td>
                        <td class="apellido"><?php echo $mascota->strApellidos ?></td>
                        <td class="terminal"><?php echo $mascota->intIdEmpleadoTerminal ?></td>
                        <td class="sueldo"  style="visibility:collapse; display:none;"> <?php echo $mascota->monSalario ?> </td>
                        <td> <a class="boton btn btn-warning">CARGAR ✅</a>				
                        </td>

                       
                    </tr>
                    <?php } ?>
            </tbody>
        </table>
    </div>





</div>

<script>
function myFunction(cedula,nombre,apellido,terminal,sueldo) {
 // alert(cedula+nombre+apellido+terminal);
    var parametros = 
          {
            "cedula" : cedula ,
            "nombre" : nombre ,
            "apellido" : apellido ,
            "terminal" : terminal ,
            "id_alm" : "<?php echo $whsTurem ; ?>" ,
            "mes" : "<?php echo $mes ; ?>" ,
            "year" : "<?php echo $year ; ?>" ,
            "sueldo" : sueldo  //pendiente campo nomina 
          };

          $.ajax({
            data: parametros,
            url: 'turemp_dbEmps.php',
            type: 'POST',
        //    timeout: 3000,
            success: function(){
           //   alert('success');
              $("#find").click();
            },
            error: function(){
              alert('error de conexion - revisa tu red');
            }
          });
}
</script>

<?php 

                    
include_once "footer.php" ?>