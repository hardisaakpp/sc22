
<?php



include_once "header.php";




    //exit();

    //header('location:listar.php');
   
        $sentencia2 = $db->query("exec sp_getStockTransitorioItemsAll" );
        $reconteos = $sentencia2->fetchAll(PDO::FETCH_OBJ);
 ?>

    <div class="row">


<!-- -----------------------------------------------------------------------------------------------------------
  --------------------------------------------------------------------------------------------------------------
  --------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TRANSFERENCIAS POR RECIBIR EN TRANSITORIAS</h1>
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
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='xls_stransitoriasAll.php';"> Descargar xls 💾 </button>
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
<!-- -----------------------------------------------------------------------------------------------------------
  --------------------------------------------------------------------------------------------------------------
  --------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------>


<div class="col-md-12">
    <div class="card">

        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Destino</th>     
                    <th>Transferencia</th>
                    <th>DocDate</th>
                    
                    <th>OpenQty</th>                      
                </tr>
            </thead>
            <tbody>
                <?php 
                
                foreach($reconteos as $reconteo){ 
                    $ToWhsCode = $reconteo->ToWhsCode;
                    $DocNum = $reconteo->DocNum;
                    $DocDate = $reconteo->DocDate;
                    $OpenQty = $reconteo->OpenQty;
                
                    ?>
                    <tr>                            
                        <td><?php echo $ToWhsCode ?></td>
                        <td><?php echo $DocNum ?></td>
                        <td><?php echo $DocDate ?></td>
                        <td><?php echo $OpenQty ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
   



    </div>
</div>


	
</div>
<?php
 
include_once "footer.php" ?>