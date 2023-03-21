<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
    $s1 = $db->query("exec sp_getTFT_resumen ".$idcab." " );
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
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>

        <form id="monthformX" method="post" action="" name="headbuscar">
   

        <input type=number id="idcab" class="form-control" name="idcab" value=<?php echo $idcab; ?> hidden >

        <input type="submit" name="find" value="Descargar .xlsx" class="form-control"
        onclick=this.form.action="TTreporteSacnsXlsx.php?idcab=<?php echo $idcab ?>">
        
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