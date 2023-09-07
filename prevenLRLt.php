<?php
    include_once "header.php";
    //si no es admin no abre
   // if ($userAdmin!=1 && $userAdmin!=3 && $userAdmin!=6){
    //    echo ('<h4> NO TIENE ACCESO</h4>');
        
    //}else
    {
        # code...

        $mes=date("n"); ;
        $anio=date("Y");


    if (isset($_POST["mes"]) and isset($_POST["anio"]) )
    {
        $mes=$_POST['mes'];
        $anio=$_POST['anio'];
        
    }

// cabecera de toma actual

//MABELTRADING
$sentencia = $db->query("
    select * ,
    CASE WHEN mes=1 THEN 'Enero'
        WHEN mes=2  THEN 'Febrero'
        WHEN mes=3  THEN 'Marzo'
        WHEN mes=4  THEN 'Abril'
        WHEN mes=5  THEN 'Mayo'
        WHEN mes=6  THEN 'Junio'
        WHEN mes=7  THEN 'Julio'
        WHEN mes=8  THEN 'Agosto'
        WHEN mes=9  THEN 'Septiembre'
        WHEN mes=10  THEN 'Octubre'
        WHEN mes=11  THEN 'Noviembre'
        ELSE 'Diciembre' 
    END as 'NameMonth'
            from VendMetas  met
        join  Vendedores_OSLP  ven on met.fk_id_empleados=ven.id
    where mes=".$mes." and anio=".$anio." and fk_emp='MT' AND whsCode like '".$userName."%' 
     ");

    
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ); 


  
    
   
        ?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>PRESUPUESTO POR VENDEDOR</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">🔃</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php';">X</button>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
<!-- /.breadcrumbs-->

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->
    
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            
        <form id="monthformX"  method="post" action="">
            <div class="input-group">
                Mes
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
                Año
                <input type="number" name="anio" min="2022" max="2025" id="anio" class="form-control" value="<?php echo $anio ?>" required>
                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="prevenLRLt.php">	
            </div>
        </form>
        <script>
            document.getElementById('mes').value = <?php echo $mes; ?>;
        </script>


        </div>
    </div>
</div>
    
<!---------------------------------------------->

<?php
 if (count($rows)==0) {
    echo ('<h4> ¡No existen registros! </h4>');
} else {
  

    ?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">HISTORIAL </strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>whsCode</th>
                                    <th>CodVendedor</th>
                                    <th>Nombre Vendedor</th>
                                    <th>Valor</th>
                                    
                                    
                                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                        <td><?php echo $citem->whsCode ?></td>
                        <td><?php echo $citem->SlpCode ?></td>
                        <td><?php echo $citem->SlpName ?></td>
                        <td> <?php echo $citem->meta ?> </td>
                        
                    </tr>
                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>



       



    <?php
        }
    }
    ?>


<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>



      
<?php  

 
  
include_once "footer.php"; ?>