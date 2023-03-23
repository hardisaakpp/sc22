<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
    $s1 = $db->query(" 
    select top 50 s.id, fecScan, barcode, a.ID_articulo, a.descripcion 
    from StockScan s
	left join Articulo a on s.barcode=a.codigoBarras
    where fk_id_stockCab=".$idcab."
    and id_user=".$userId."
    order by fecScan desc
    " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);    

?>


<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>TOMA FISICA TOTAL <?php  echo $idcab ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8"> 
                    
                    <div class="page-header float-right">

                        <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Acciones
                        </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <button type="button" class="dropdown-item" onclick="window.location.href='TTscans.php?idcab=<?php echo $idcab ?>'">ESCANEAR</button>
                                <button type="button" class="dropdown-item" onclick="window.location.href='TTscanRes.php?idcab=<?php echo $idcab ?>'">RESUMEN</button>
                                <button type="button" class="dropdown-item" onclick="window.location.href='wllcm.php'">SALIR</button>
                            </div>
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
                <strong class="card-title">50 ULTIMOS CODIGOS INGRESADOS </strong>
            </div>
            <div class="card-body">
              
          

            <table  class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Codigo Barras</th>
                        <th>Item Code</th>
                        <th>Descripcion</th>
                        <th></th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php  

                
                foreach($scans as $user){ ?>
                    <tr>
                        <td><?php echo $user->fecScan ?></td>
                        <td><?php echo $user->barcode ?></td>
                        <td><?php echo $user->ID_articulo ?></td>
                        <td><?php echo $user->descripcion ?></td>
                        <td name='op'><a class="btn btn-warning btn-sm delete" onclick ="delete_user($(this),<?php echo $user->id ?>)">❌</a></td>
                    </tr>
                   
                <?php 

            } 
            
          
            
            ?>   
                </tbody>
            </table>

                    
                   
            </div>
            
                <!-- </form>-->
        </div>
    </div>

<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
<script>
    function delete_user(row,id)
        { 
          //  alert(id);
            delTD(id,row);
            //row.closest('tr').remove();
        }

    function delTD(id,row) {
        
        var parametros = 
            {
                "id" : id
            };

            $.ajax({
                data: parametros,
                url: 'php/scanDelete.php',
                type: 'GET',
                async: false,
                success: function(data){
                    row.closest('tr').remove();
                    Swal.fire({
                    position: 'top-end',
                    icon: 'Eliminado',
                    title: 'Se elimino 1 registro',
                    showConfirmButton: false,
                    timer: 1500
                    })

                },
                error: function(){
                    console.log('error de conexion - revisa tu red');
                }
            });
    }
</script>
<?php   
include_once "footer.php";
 ?>