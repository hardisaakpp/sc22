<?php
    include_once "header.php";
    //si no es admin no abre
    if($whsCica==0){
        echo ('<h4> NO TIENE ASIGNADO UN ALMACEN PARA CIERRES DE CAJA</h4>');
        
    }else{
        # code...
    
// cabecera de toma actual
    $sentencia = $db->prepare("
    
    
select a.id, d.fecha, concat(d.whsCode,' - ',a.nombre) as almacen,
sum(d.valRec+ d.valOnline+ d.valPinpadOn+ d.valPinpadOff - d.Valor) as 'Diferencia'
from CiCaSAP d join Almacen a on d.whsCode=a.cod_almacen
where d.fecha > DATEADD(MONTH,-2,GETDATE()) and a.id=?
group by d.fecha, d.whsCode,a.id,a.nombre
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
                    <th>ALMACEN</th>
                    <th>DIFERENCIA</th>
                    <th>.</th>

                </tr>
                </thead>
                <tbody>
                <?php   foreach($rows as $citem){ ?>


                    <tr>
                                    <td><?php echo $citem->id ?></td>
                                    <td><?php echo $citem->fecha ?></td>
                                    <td><?php echo $citem->almacen ?></td>
                                    <td><?php echo $citem->Diferencia  ?></td>
                                    <td><button type="button" class="btn btn-outline-success" 
                                    onclick="window.location.href='cica.php?pFecha=<?php echo $citem->fecha ?>&pIdAlmacen=<?php echo $citem->id ?>'"
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