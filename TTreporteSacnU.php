<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];



   
   $sU1 = $db->query("select c.fecScan, username, c.barcode, a.ID_articulo, a.descripcion, a.nombreGrupo
   from StockScan c
       join users u on c.id_user=u.id
       left join Articulo a on c.barcode=a.codigoBarras
   where fk_id_stockCab=".$idcab."
   order by 1 desc  " );
   $zUcans = $sU1->fetchAll(PDO::FETCH_OBJ); 

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
                                  <div class="dropdown-divider"></div>
                                  <input type="submit"class="dropdown-item" value="Scans por usuario" onclick=this.form.action="TTreporteScansUXlsx.php?idcab=<?php echo $idcab ?>">
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


<div class="col-md-12">
        <div class="card">
             <div class="card-header">
                <strong class="card-title">SCAN POR USUARIO</strong>
            </div>
            <div class="card-body">
              
          

            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>fecScan</th>
                        <th>username</th>
                        <th>barcode</th>
                        <th>ID_articulo</th>    
                        <th>descripcion</th>
                        <th>nombreGrupo</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php  

                
                foreach($zUcans as $user){ ?>
                    <tr>
                        <td><?php echo $user->fecScan ?></td>
                        <td><?php echo $user->username ?></td>
                        <td><?php echo $user->barcode ?></td>
                        <td><?php echo $user->ID_articulo ?></td>
                        <td><?php echo $user->descripcion ?></td>
                        <td><?php echo $user->nombreGrupo ?></td>
                
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

<?php   
include_once "footer.php";
 ?>