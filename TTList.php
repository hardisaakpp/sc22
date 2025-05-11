<?php
    include_once "header.php";
    //si no es admin no abre
    
            
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items, c.locked
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where (tipo='TT' or tipo='TP') and [date]>DATEADD(MONTH,-1,getdate()) and c.locked=0
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) , c.locked
        order by CONCAT(date,' ',left(time,5)) desc
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ);   
?>



  

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMAS FISICAS TOTALES</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">F5</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'">X</button>
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


<!-- Widgets  -->
    <div class="row">

    <?php   foreach($users as $user){ ?>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-3">
                        <a href="TTscans.php?idcab=<?php echo $user->id?>">
                            <i class="pe-7s-browser"></i>
                            </a>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count"><?php echo $user->items ?></span> items</div>
                                <div class="stat-text"><?php echo 'Conteo N°.'.$user->id?></div>
                                <div class="stat-heading"><?php echo $user->cod_almacen .' al '.$user->fec ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?> 

        

    </div>


                 
               


<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   
include_once "footer.php";
 ?>