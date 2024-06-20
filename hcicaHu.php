<?php
    include_once "header.php";
    //si no es admin no abre
    if($whsCica==0){
        echo ('<h4> NO TIENE ASIGNADO UN ALMACEN PARA CIERRES DE CAJA</h4>');
        
    }else{
        # code...
    
// cabecera de toma actual
    $sentencia = $db->prepare("

    select distinct c.fecha, ac.cod_almacen, aC.id AS id_alm, ac.nombre as whsName,
		
    ISNULL(QHI.ValHitell, 0) as ValHitell  , ISNULL(QHI.Diferencia, 0)  as DifTienda, 
    CASE
        WHEN QHI.ValHitell IS NULL THEN 'NULL'
        ELSE c.[status]
    END AS [status]
    , c.cerrado
from cica c join Almacen ac on c.fk_ID_almacen=ac.id
left join (
        select h.fecha, a.id as id_alm, sum(h.Valor) as ValHitell
        ,sum(h.valRec+ h.valOnline+ h.valPinpadOn+ h.valPinpadOff - h.Valor) as 'Diferencia'
        from CiCaHitell h join Almacen a on h.whsCode=a.cod_almacen
        where h.fecha  > DATEADD(MONTH,-1,GETDATE()) 
        group by h.fecha, a.id
        ) QHI on c.fk_ID_almacen=QHI.id_alm and c.fecha=QHI.fecha
where c.fecha > DATEADD(MONTH,-1,GETDATE())
AND  aC.id=?
     ");
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
                            <h1>CIERRES DE CAJA</h1>
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
                   <!-- <th>ID</th>-->
                    <th>FECHA</th>
                    <th>ALMACEN</th>
                    <th>ESTADO</th>
                    <th>Diferencia Tienda</th>
                    <th>.</th>

                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                                 <!--   <td><?php echo $citem->id ?></td>-->
                                    <td><?php echo $citem->fecha ?></td>
                                    <td><?php echo $citem->cod_almacen ?></td>

                                    <td><?php 
                                    
                                    if ($citem->cerrado==1) {
                                        echo '🔒 Cerrado';
                                    }else {
                                        echo '🔓 Abierto';
                                    }

                                   ?></td>

                                    <td><?php echo $citem->DifTienda  ?></td>
                                    <td><button type="button" class="btn btn-outline-success" 
                                    onclick="window.location.href='hcica.php?pFecha=<?php echo $citem->fecha ?>&pIdAlmacen=<?php echo $citem->id_alm ?>'"
                                    > 👁️‍🗨️ </button>  </td>
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