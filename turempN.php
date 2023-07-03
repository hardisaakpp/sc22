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





   $sentencia3 = $dbB->query("SELECT distinct
                strIdentificacion,strNombres,strApellidos
            FROM [dbo].[tblEmpleados]
             " );

   $mascotas = $sentencia3->fetchAll(PDO::FETCH_OBJ);


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
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="js/syntax/shCore.js"></script>
	<script type="text/javascript" language="javascript" src="js/demo.js"></script>
	<script type="text/javascript" language="javascript" class="init">
            $(document).ready(function() {
            $('#example').DataTable();
            } );
    </script>

<script>
    document.getElementById('mes').value = <?php echo $mes; ?>;



    function indexesLX(){

    try {
        $(".boton").click(function() {
            $('#example').DataTable();
            
        var cedula='';
        // Obtenemos todos los valores contenidos en los <td> de la fila
        // seleccionada
        $(this).parents("tr").find(".cedula").each(function() {
            cedula += $(this).html() + "\n";
        });
        console.log(cedula);
        var nombre='';
        $(this).parents("tr").find(".nombre").each(function() {
            nombre += $(this).html() + "\n";
        });
        console.log(nombre);
        var apellido='';
        $(this).parents("tr").find(".apellido").each(function() {
            apellido += $(this).html() + "\n";
        });
        console.log(apellido);

        myFunction(cedula,nombre, apellido);
        });
    }
        catch(x) { /* puede usarse cualquier otro nombre en lugar de 'x' */
        //document.getElementById("ejemplo").innerHTML = x.message;
        console.log(x.message);
    }

        
    // setInterval('contadoradd()',2000);
    }

   
</script>

<div class="content">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title"><?php echo $mes."/".$year ; ?> - Nuevo Usuario</strong>
            </div>
            <div class="card-body">


            <table class="table table-bordered display" id="example">
                    <thead>
                        <tr>
                            <th>CEDULA</th>
                            <th>NOMBRES</th>
                            <th>APELLIDOS</th>
                    
                            <th>.</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php   foreach($mascotas as $mascota){   ?>


                        <tr>
                            <td class="cedula"><?php echo $mascota->strIdentificacion ?></td>
                            <td class="nombre"><?php echo $mascota->strNombres ?></td>
                            <td class="apellido"><?php echo $mascota->strApellidos ?></td>
                                                
                            <td> <a class="boton btn btn-warning">CARGAR ✅</a>	</td>

                        </tr>
                    
                    <?php } ?>   
                    </tbody>
                </table>
            </div>    
        </div> 
    </div> 
</div> 


<script>
    function myFunction(cedula,nombre,apellido) {
    // console.log(cedula+nombre+apellido);
        var parametros = 
          {
            "cedula" : cedula ,
            "nombre" : nombre ,
            "apellido" : apellido ,
          
            "id_alm" : "<?php echo $whsTurem ; ?>" ,
            "mes" : "<?php echo $mes ; ?>" ,
            "year" : "<?php echo $year ; ?>" 

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