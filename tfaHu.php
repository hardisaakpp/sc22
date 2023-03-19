<?php
    include_once "header.php";
    //si no es admin no abre
    if($whsInvs==0){
        echo ('<h4> NO TIENE ASIGNADO UN ALMACEN PARA REALIZAR INVENTARIO</h4>');
        
    }else{
        # code...
    
// cabecerqa de toma actual
    $sentencia = $db->prepare("SELECT s.id as id_cab, FK_ID_almacen as id_alm, concat(date,' ',time) as fec, da.responsable, INI, REC, FIN
    FROM StockCab s 
        join vw_stockDet_pivotStatus p on s.id=p.FK_id_StockCab
        left join StockCab_TFA da on s.id=da.fk_id_StockCab
    WHERE tipo='TF' AND [date]> DATEADD(MONTH,-2,GETDATE()) AND FK_ID_almacen= ? ");
    $sentencia->execute([$whsInvs]);
    
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($rows)==0) {
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
                            <h1>TOMAS FISICAS ALEATORIAS</h1>
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
            <strong class="card-title">HISTORIAL ULTIMOS DOS MESES</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                <tr>
                                    <th>ID</th>
                                    <th>FECHA</th>
                                    <th>RESPONSABLE</th>
                                    <th>CUMPLIMIENTO</th>
                                    <th>ID</th>

                                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                                    <td><?php echo $citem->id_cab ?></td>
                                    <td><?php echo $citem->fec ?></td>
                                    <td><?php echo $citem->responsable ?></td>
                                    <td><?php echo (($citem->FIN*100)/($citem->FIN+$citem->INI+$citem->REC)).'%'  ?></td>
                                    <td><button type="button" class="btn btn-outline-success" onclick=window.open("<?php echo 'tfaDprint.php?idcab=' . $citem->id_cab ?>","demo","toolbar=0,status=0,");> 👁️‍🗨️ </button>  </td>
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