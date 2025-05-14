<?php session_start(); if(!isset($_SESSION['username']) || !isset($_SESSION['perfil'])){ session_unset(); session_destroy(); header('location:index.php'); exit(); } ?>
<?php
    include_once "header.php";
    if($userAdmin!=1 && $userAdmin!=3){ echo ('<h4> NO TIENE ACCESO</h4>'); exit(); }
    else{
        $conteo=0;
        $reconteo=0;
        $cerrado=0;
        $diferencias=0;
        if(isset($_POST["conteo"])){ $conteo=$_POST["conteo"]; if($conteo=='on'){ $conteo=1; } }
        if(isset($_POST["reconteo"])){ $reconteo=$_POST["reconteo"]; if($reconteo=='on'){ $reconteo=1; } }
        if(isset($_POST["cerrado"])){ $cerrado=$_POST["cerrado"]; if($cerrado=='on'){ $cerrado=1; } }
        if(isset($_POST["diferencias"])){ $diferencias=$_POST["diferencias"]; if($diferencias=='on'){ $diferencias=1; } }
        $wheres = '';
        if($conteo==1){ $wheres = $wheres." and INI>0 "; }
        if($reconteo==1){ $wheres = $wheres." and REC>0 "; }
        if($cerrado==1){ $wheres = $wheres." and FIN>0 "; }
        if($diferencias==1){ $wheres = $wheres." and NOVEDADES>0 "; }
        if(!isset($_POST["desde"]) and !isset($_POST["hasta"])){ $desde=Date('Y-m-d') ; $hasta=Date('Y-m-d'); }else{ $desde=$_POST['desde']; $hasta=$_POST['hasta']; }
        $q="SELECT s.id AS id_cab, FK_ID_almacen AS id_alm, concat(DATE,' ',TIME) AS fec, 
            da.responsable, INI, REC, FIN, a.cod_almacen, a.nombre, isnull(nov.NOVEDADES,0) AS NOVEDADES
            FROM StockCab s 
                JOIN Almacen a ON s.FK_ID_almacen=a.id
                JOIN vw_stockDet_pivotStatus p ON s.id=p.FK_id_StockCab
                LEFT JOIN StockCab_TFA da ON s.id=da.fk_id_StockCab
                LEFT JOIN (SELECT FK_id_StockCab AS idcab, COUNT(*) AS NOVEDADES FROM StockDet WHERE estado='FIN' AND reconteo<>stock GROUP BY FK_id_StockCab) nov ON s.id=nov.idcab
            WHERE tipo='TF' $wheres AND [date] BETWEEN '$desde' AND '$hasta'";
        $r=resp_simdim($q); ?>

        <div class="breadcrumbs bg-body text-body dark:bg-dark dark:text-light py-1">
            <div class="container-fluid">
                <div class="row m-0">
                    <div class="col-sm-6">
                        <h4>TOMAS FISICAS ALEATORIAS</h4>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <button type="button" class="btn btn-outline-warning" onclick="location.reload();"><i class="bi bi-arrow-clockwise"></i></button>
                        <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php';"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">    
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">                        
                        <form id="monthformX"  method="post" action="">
                            <div class="input-group" style="font-size: 0.8rem;">
                                <strong>Rango fecha</strong> &emsp;
                                <input type="date" name="desde" id="desde" class="form-control" value="<?php echo $desde ?>" style="font-size: 0.8rem;" required>
                                <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo $hasta ?>" style="font-size: 0.8rem;" required>
                                <?php 
                                    if($conteo==1){ echo "<label>&emsp;Conteo<input type='checkbox' name='conteo' checked ></label>"; }
                                    else{ echo "<label>&emsp;Conteo<input type='checkbox' name='conteo'></label>"; }

                                    if($reconteo==1){ echo "<label>&emsp;Reconteo<input type='checkbox' name='reconteo' checked ></label>"; }
                                    else{ echo "<label>&emsp;Reconteo<input type='checkbox' name='reconteo'></label>"; }

                                    if($cerrado==1){ echo "<label>&emsp;Cerrado<input type='checkbox' name='cerrado' checked ></label>"; }
                                    else{ echo "<label>&emsp;Cerrado<input type='checkbox' name='cerrado'></label>"; }

                                    if($diferencias==1){ echo "<label>&emsp;Diferencia<input type='checkbox' name='diferencias' checked ></label>"; }
                                    else{ echo "<label>&emsp;Diferencia<input type='checkbox' name='diferencias'></label>&emsp;"; }
                                ?>
                                <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control" onclick=this.form.action="tfaL.php" style="font-size: 0.8rem;">
                            </div>
                        </form>
                    </div>
                </div>
            </div>    
            <?php if(count($r)==0){ echo ('<h4> ¡No existen registros! </h4>'); }
            else{ ?>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">HISTORIAL ULTIMO MES</strong>
                        </div>
                        <div class="container">
                            <div class="table-responsive">
                                <table id="employee_data" class="table table-striped table-bordered nowrap" style="width:100%" style="font-size: 0.7rem;">
                                    <thead align="left" style="background: #a6acaf">
                                        <tr width="100%" align="center" style="font-size: 0.7rem;">
                                            <th>ID</th>
                                            <th>BODEGA</th>
                                            <th>FECHA</th>
                                            <th>RESPONSABLE</th>
                                            <th>CONTEO</th>
                                            <th>RECONTEO</th>
                                            <th>CERRADO</th>
                                            <th>CUMPLIMIENTO</th>
                                            <th>DIFERENCIAS</th>
                                            <th>ACCIÓN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($r as $f){ ?>
                                            <tr align="center" style="font-size: 0.7rem;">
                                                <td><?php echo $f['id_cab'] ?></td>
                                                <td><?php echo $f['cod_almacen'] ?></td>
                                                <td><?php echo $f['fec'] ?></td>
                                                <td><?php echo $f['responsable'] ?></td>
                                                <td><?php echo $f['INI'] ?></td><td><?php echo $f['REC'] ?></td><td><?php echo $f['FIN'] ?></td>
                                                <td><?php echo (($f['FIN']*100)/($f['FIN']+$f['INI']+$f['REC'])).'%'  ?></td>
                                                <td><?php echo $f['NOVEDADES'] ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-success" onclick=window.open("<?php echo 'tfaDprintAdm.php?idcab=' . $f["id_cab"] ?>","demo","toolbar=0,status=0,");> 👁️‍🗨️ </button>                                                  
                                                    <button type="button" class="btn btn-warning delete" onclick="delete_user($(this),<?php echo $f['id_cab'] ?>)" ><i class="bi bi-trash"></i></button> 
                                                </td>
                                            </tr>
                                    
                                    <?php } ?>   
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <script> 
                    function delete_user(row,id){ 
                        if(confirm("¿Seguro de eliminar?")){
                            delTD(id,row);
                        }else{
                            console.log('FALSO!');
                        }
                    }
                    
                    function delTD(id,row){    
                        var parametros = { "id" : id };
                            $.ajax({
                                data: parametros,
                                url: 'php/deleteTFA.php',
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
            <?php } ?>
        </div> 
    <?php } ?>
     
<?php include_once "footer.php"; ?>