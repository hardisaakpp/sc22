<?php
    include_once "header.php";
    //si no es admin no abre
    if ($userAdmin!=1 && $userAdmin!=3 && $userAdmin!=6){
        echo ('<h4> NO TIENE ACCESO</h4>');
        
    }else{
        # code...

        $desde=Date('Y-m-d') ;
        $hasta=Date('Y-m-d') ;


    if (isset($_POST["desde"]) and isset($_POST["hasta"]) )
    {
        $desde=$_POST['desde'];
        $hasta=$_POST['hasta'];
        
    }

// cabecera de toma actual


$sentencia = $db->query("
        SELECT *
        FROM [STORECONTROL].[dbo].[vw_tfa_fecini_fecfin]
        where fecFin between '".$desde."' and '".$hasta."'  
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
                            <h1>TOMAS FISICAS ALEATORIAS REALIZADAS</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                              <!--  <button type="button" class="btn btn-outline-warning" onclick="location.reload();">🔃</button>-->
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
                Rango fecha
                <input type="date" name="desde" id="desde" class="form-control" value="<?php echo $desde ?>" required>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo $hasta ?>" required>
                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="tfaRe.php">	
            </div>
        </form>



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
                                    <th>ID</th>
                                    <th>BODEGA</th>
                                    <th>FECHA</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Responsable</th>
          
                                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                                    <td><?php echo $citem->fk_id_StockCab ?></td>
                                    <td><?php echo $citem->ALMACEN ?></td>
                                    <td> <?php echo $citem->fecFin ?> </td>
                                    <td> <?php echo $citem->fecInicio ?> </td>
                                    <td><?php echo $citem->hhFin ?></td>
                                    
                                    <td><?php echo $citem->responsable ?></td>
                                

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