<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
    $s1 = $db->query("exec sp_getTFT_resumenSum ".$idcab." " );
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


        
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Descargar Xlsx
                        </button>
                        <td>
                            <button type="button" class="btn btn-outline-success" 
                                    onclick="window.location.href='php/tftToINI.php?idcab=<?php echo $idcab ?>'"
                             > Cerrar Captura </button>  
                        </td>
                        <td><button type="button" class="btn btn-outline-success" 
                                    onclick="window.location.href='tftD.php?idcab=<?php echo $idcab ?>'"
                             > Reconteo </button>  
                        </td>
                     
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                           
                                  <input type="submit"class="dropdown-item" value="Consolidado" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=1&idcab=<?php echo $idcab ?>">
                                  <div class="dropdown-divider"></div>
                                  <input type="submit"class="dropdown-item" value="Cerrados" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=2&idcab=<?php echo $idcab ?>">
                                  <input type="submit"class="dropdown-item" value="Diferencias" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=3&idcab=<?php echo $idcab ?>">
                                  
                                  
                                  <input type="submit"class="dropdown-item" value="Otros Codigos" onclick=this.form.action="TTreporteSacnsXlsx.php?ti=4&idcab=<?php echo $idcab ?>">
                                  <div class="dropdown-divider"></div>
                                  <input type="submit"class="dropdown-item" value="Scans por usuario" onclick=this.form.action="TTreporteScansUXlsx.php?idcab=<?php echo $idcab ?>">

                                  <div class="dropdown-divider"></div>
                                  <input type="submit"class="dropdown-item" value="Final Reconteo" onclick=this.form.action="TTreporteSacnsXlsxRec.php?ti=1&idcab=<?php echo $idcab ?>">
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

<?php   foreach($scans as $scan){ ?>
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

    
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="stat-widget-five">
                    <div class="stat-icon dib flat-color-1">

                        <i class="pe-7s-cart"></i>

                    </div>
                    <div class="stat-content">
                        <div class="text-left dib">
                            <div class="stat-text"><span class="count">9999 </span> items</div>
                            <div class="stat-heading">
                                
                            <a href="TTreporteSacns.php?idcab=<?php echo $idcab?>&tipo=DIFPOS">
                                <?php echo 'DIFERENCIAS POSITIVAS'  ?>
                            </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
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