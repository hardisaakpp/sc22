<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('ACCESO DENEGADO');
    
        }else {
            
        
        $s1 = $db->query("select * from Almacen" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);   
        
        $s1 = $db->query("
        select c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) as fec, count(d.id) as items
        from StockCab c
        join Almacen a on c.FK_ID_almacen=a.id
        left join StockDet d on c.id=d.FK_id_StockCab 
        where (tipo='TT' or tipo='TP') and [date]>DATEADD(MONTH,-1,getdate())
        group by c.id, a.cod_almacen, CONCAT(date,' ',left(time,5)) 
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


<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">GENERADAS LOS ULTIMOS 30 DIAS</strong>
        </div>
        <div class="card-body">
            <table class="table" id='tblBodegas'>
                <thead>
                    <tr>
                        <th>CODIGO</th>
                        <th>ALMACEN</th>
                        <th>FECHA</th>
                        <th>ITEMS</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($users as $user){ ?>


                    <tr>
                        <td><?php echo $user->id ?></td>
                        <td><?php echo $user->cod_almacen ?></td>
                        <td><?php echo $user->fec ?></td>
                        <td><?php echo $user->items ?></td>
                        <td> 
                            <button type="button" class="btn btn-outline-success" onclick="confirmar('<?php echo $user->id ?>')">
                                ACTUALIZAR
                            </button>
                        </td>
                    </tr>                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>

<script> 
    function confirmar(idcab) {
    
        if (confirm("¿Seguro de actualizar?")) {
            $(".loader-page").css({visibility:"visible",opacity:"0.8"});
                window.location.href='php/refreshTFT.php?idcab='+idcab;
            }
    };
</script>
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   }; 
include_once "footer.php";
 ?>