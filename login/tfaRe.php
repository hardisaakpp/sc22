<?php session_start(); if(!isset($_SESSION['username']) || !isset($_SESSION['perfil'])){ session_unset(); session_destroy(); header('location:index.php'); exit(); } ?>
<?php
    include_once "header.php";
    if($userAdmin!=1 && $userAdmin!=3 && $userAdmin!=6){ echo ('<h4>NO TIENE ACCESO</h4>'); exit(); }
    else{       
        if(isset($_POST["desde"]) && isset($_POST["hasta"])){ $desde=$_POST['desde']; $hasta=$_POST['hasta']; }else{ $desde=Date('Y-m-d'); $hasta=Date('Y-m-d'); }
        $q="SELECT ALMACEN,fecFin,hhFin,fk_id_StockCab,fecInicio,responsable FROM STORECONTROL.dbo.vw_tfa_fecini_fecfin where fecFin between '$desde' and '$hasta'";
        $r=resp_simdim($q); ?>

        <div class="breadcrumbs bg-body text-body dark:bg-dark dark:text-light py-1">
            <div class="container-fluid">
                <div class="row m-0">
                    <div class="col-sm-6">
                        <h4>TOMAS FISICAS ALEATORIAS REALIZADAS</h4>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">    
            <div class="col-md-12">
                <div class="card">
                    <div class="container">                        
                        <form id="monthformX"  method="post" action="">
                            <div class="input-group" style="font-size: 0.8rem;">
                                <strong>Rango fecha</strong> &emsp;
                                <input type="date" name="desde" id="desde" class="form-control" value="<?php echo $desde ?>" style="font-size: 0.8rem;" required>
                                <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo $hasta ?>" style="font-size: 0.8rem;" required>
                                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="tfaRe.php" style="font-size: 0.8rem;">	
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
            if(empty($r)){ echo ('<h4> ¡No existen registros! </h4>'); }
            else{ ?>
                <div class="container">
                    <div class="table-responsive">
                        <table id="employee_data" class="table table-striped table-bordered nowrap" style="width:100%" style="font-size: 0.8rem;">
                            <thead align="left" style="background: #a6acaf">
                                <tr width="100%" align="center" style="font-size: 0.9rem;">
                                    <td><strong>ID</strong></td> 
                                    <td><strong>BODEGA</strong></td> 
                                    <td><strong>FECHA</strong></td>
                                    <td><strong>INICIO</strong></td>
                                    <td><strong>FIN</strong></td>
                                    <td><strong>RESPONSABLE</strong></td>   
                                </tr>  
                            </thead>  
                            <?php foreach($r as $a){ ?>
                                <tr align="center"  style="font-size: 0.8rem;"> 
                                    <td><?php echo isset($a['fk_id_StockCab']) ? $a['fk_id_StockCab'] : ''; ?></td>
                                    <td align="left"><?php echo isset($a['ALMACEN']) ? $a['ALMACEN'] : ''; ?></td>
                                    <td><?php echo isset($a['fecFin']) ? date('Y-m-d', strtotime($a['fecFin'])) : ''; ?></td>
                                    <td><?php echo isset($a['fecInicio']) ? date('H:i:s', strtotime($a['fecInicio'])) : ''; ?></td>
                                    <td><?php echo isset($a['hhFin']) ? date('H:i:s', strtotime($a['hhFin'])) : ''; ?></td>
                                    <td align="left"><?php echo isset($a['responsable']) ? $a['responsable'] : ''; ?></td>
                                </tr>  
                            <?php } ?>                                
                        </table>
                    </div>
                </div>
            <?php } ?>
       </div>
    <?php } ?>     
<?php include_once "footer.php"; ?>