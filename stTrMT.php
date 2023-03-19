<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin==2){
        echo ('<h4> NO TIENE PERMISO PARA ACCEDER </h4>');
        
    }else{
        # code...
    
// cabecerqa de toma actual

$sentencia2 = $db->query("exec sp_getStockTransitorioAllMT" );
    $reconteos = $sentencia2->fetchAll(PDO::FETCH_OBJ);
    
    
    if (count($reconteos)==0) {
        echo ('<h4> ¡No existen registros! </h4>');
    } else {
      
    
        ?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>BODEGAS TRANSITORAS MABEL TRADING S.A.</h1>
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
        <div class="card-header">
            <strong class="card-title">STOCK ACTUAL</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>ORIGEN</th>
                                    <th>STOCK NORMAL</th>
                                    <th>STOCK > 72 HORAS</th>

                                </tr>
                </thead>
                <tbody>
                <?php   foreach($reconteos as $citem){ ?>


                    <tr>
                                    <td><?php echo $citem->ToWhsCode ?></td>
                                    <td><?php echo $citem->NORMAL ?></td>
                                    <td><?php echo $citem->RETRASADO ?></td>
                                
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