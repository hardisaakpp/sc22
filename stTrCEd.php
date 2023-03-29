<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin==2){
        echo ('<h4> NO TIENE PERMISO PARA ACCEDER </h4>');
        
    }else{
        # code...
    
// cabecerqa de toma actual
$idWhs = $_GET["id"];
$sentencia2 = $db->query("exec sp_getStockTransitorioItems '". $idWhs ."' " );
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
                            <h1>BODEGAS TRANSITORIAS COSMETICOS DEL ECUADOR</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">🔃</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='stTrCE.php';">X</button>
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
            <strong class="card-title">STOCK EN <?php echo $idWhs ?></strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>DocNum</th>
                    <th>DocDate</th>
                    <th>ItemCode</th>
                    <th>Quantity</th>   
                    <th>OpenQty</th>
                </tr>
                </thead>
                <tbody>
                <?php   
                foreach($reconteos as $reconteo){ 
                    $DocNum = $reconteo->DocNum;
                    $DocDate = $reconteo->DocDate;
                    $ItemCode = $reconteo->ItemCode;
                    $Quantity = $reconteo->Quantity;
                    $OpenQty = $reconteo->OpenQty;
                
                    ?>
                    <tr>                            
                        <td><?php echo $DocNum ?></td>
                        <td><?php echo $DocDate ?></td>
                        <td><?php echo $ItemCode ?></td>
                        <td><?php echo $Quantity ?></td>
                        <td><?php echo $OpenQty ?></td>
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