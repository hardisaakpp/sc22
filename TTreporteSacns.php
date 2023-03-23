<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   $tipo=$_GET["tipo"];

   if ($tipo=='CERRADOS') {
    $s1 = $db->query("exec sp_getTFT_resumen ".$idcab.",2 " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);  
   } else  if ($tipo=='DIFERENCIAS') {
    $s1 = $db->query("exec sp_getTFT_resumen ".$idcab.",3 " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);  
   } else {
    $s1 = $db->query("exec sp_getTFT_resumen ".$idcab.",4 " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);  
   }
   
   $s1 = $db->query("exec sp_getTFT_resumenSum ".$idcab." " );
   $zcans = $s1->fetchAll(PDO::FETCH_OBJ);    
  

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
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>

        <form id="monthformX" method="post" action="" name="headbuscar">
   

        <input type=number id="idcab" class="form-control" name="idcab" value=<?php echo $idcab; ?> hidden >


        
        <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Descargar Xlsx
                        </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                           
                                  <input type="submit"class="dropdown-item" value="Consolidado" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=1&idcab=<?php echo $idcab ?>">
                                  <div class="dropdown-divider"></div>
                                  <input type="submit"class="dropdown-item" value="Cerrados" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=2&idcab=<?php echo $idcab ?>">
                                  <input type="submit"class="dropdown-item" value="Diferencias" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=3&idcab=<?php echo $idcab ?>">
                                  
                                  <input type="submit"class="dropdown-item" value="Otros Codigos" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=4&idcab=<?php echo $idcab ?>">
                            </div>
                        </div>
        
        </form>

                                
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

<?php   foreach($zcans as $scan){ ?>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-1">

                        <i class="pe-7s-cart"></i>

                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                            <div class="stat-text"><span class="count"><?php echo $scan->items ?></span> items</div>
                            <div class="stat-heading">
                                
                            <a href="TTreporteSacns.php?idcab=<?php echo $idcab?>&tipo=<?php echo $scan->TIPO?>">
                                <?php echo $scan->TIPO  ?>
                            </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?> 

    

</div>


<div class="col-md-12">
        <div class="card">
             <div class="card-header">
                <strong class="card-title">50 ULTIMOS CODIGOS INGRESADOS </strong>
            </div>
            <div class="card-body">
              
          

            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Codigo Barras</th>
                        <th>Item Code</th>
                        <th>Descripcion</th>
                        <th>Stock</th>
                        <th>Scan</th>
                        <th>Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                <?php  

                
                foreach($scans as $user){ ?>
                    <tr>
                        <td><?php echo $user->codigoBarras ?></td>
                        <td><?php echo $user->ID_articulo ?></td>
                        <td><?php echo $user->descripcion ?></td>
                        <td><?php echo $user->stock ?></td>
                        <td><?php echo $user->scans ?></td>
                        <td><?php echo $user->scans-$user->stock ?></td>
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