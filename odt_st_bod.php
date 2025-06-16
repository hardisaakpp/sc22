<?php
    session_start();
    if(isset($_SESSION['pick_in']) || !empty($_SESSION['pick_in'])){ $resultado=$_SESSION['pick_in']; }
    else{
        unset($_SESSION['st']);
        unset($_SESSION['sts']);
        unset($_SESSION['odc']);
        $pr=json_decode($_POST['sts_odcs'], true);
        unset($_POST['bootstrap-data-table6_length']); 
        unset($_POST['sts_odcs']);
        $st_odc = [];
        foreach($pr as $k => $v){ if(is_array($v) && isset($v['odc'])){ $st_odc[$k]=$v['odc']; $odc[$v['odc']]=true; } }
        unset($pr);
        $st_odc = $st_odc + $_POST;
        unset($_POST);
        include_once "../cx/bd_scs.php"; 
        foreach($odc as $k=>$v){
            $q="SELECT d.CodeBars,SUM(d.Quantity) AS cantidad
                FROM BISTAGING.dbo.STG_OrdenCompraCabecera AS c
                INNER JOIN BISTAGING.dbo.STG_OrdenCompraDetalle AS d ON d.DocEntry = c.DocEntry
                WHERE c.CANCELED = 'N' AND c.DocStatus = 'O' AND c.DocNum = $k
                GROUP BY d.CodeBars
            ";
            $odc_d[$k]=resp_simdim($q); 
        }
        foreach($st_odc as $kst=>$vst){
            $q="SELECT d.CodeBars,SUM(d.Quantity) AS cantidad 
                FROM STORECONTROL.dbo.SotCab_MT AS c
                INNER JOIN STORECONTROL.dbo.SotDet_MT AS d ON d.DocNum_Sot=c.DocNum
                WHERE c.DocNum=$kst
                GROUP BY d.CodeBars
            ";
            $st_d[$kst]=resp_simdim($q);
            $st_d[$kst]['odc'] = $vst;
        }
        $resultado = [];
        foreach($odc_d as $odcKey => $odcItems){
            foreach($odcItems as $item_odc){
                if(!isset($item_odc['CodeBars'])) continue;
                $code = $item_odc['CodeBars'];
                $cantidad_odc = $item_odc['cantidad'];
                $cantidad_st_total = 0;
                foreach($st_d as $stData){
                    if(!is_array($stData) || !isset($stData['odc']) || $stData['odc'] != $odcKey) continue;
                    foreach($stData as $item_st){
                        if(!isset($item_st['CodeBars'])) continue;
                        if($item_st['CodeBars'] == $code){ $cantidad_st_total += $item_st['cantidad']; }
                    }
                }
                $cantidad_restante = $cantidad_odc - $cantidad_st_total;
                $resultado[] = [
                    'odc' => $odcKey,
                    'CodeBars' => $code,
                    'cantidad_odc' => $cantidad_odc,
                    'cantidad_st' => $cantidad_st_total,
                    'cantidad_restante' => $cantidad_restante
                ];
            }
        }
        $_SESSION['pick_in']=$resultado;
    }
    include_once "header.php";
?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>LISTA PRODUCTO - UBICACIÓN</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                    <button type="button" class="btn btn-outline-danger" onclick="abrirModalCancelar()"  data-toggle="tooltip" title="Cancelar y volver">X</button>
                                    <div id="modalCancelar" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6); z-index:1000;">
                                        <div style="background:#fff; max-width:400px; margin:100px auto; padding:20px; border-radius:5px; text-align:center;">
                                            <h5><strong>¿Cancelar y salir?</strong></h5>
                                            <p>Se perderán todos los datos seleccionados. ¿Deseas continuar?</p>
                                            <button class="btn btn-success" onclick="cerrarModalCancelar()" style="margin-right:10px;" autofocus>No, quedarme</button>
                                            <button class="btn btn-danger" onclick="window.location.href='buscar_odc.php'">Sí, cancelar</button>
                                        </div>
                                    </div>
                                    <script>
                                        function abrirModalCancelar() {
                                            document.getElementById('modalCancelar').style.display = 'block';
                                            // Enfocar el botón de cancelar
                                            setTimeout(() => {
                                                document.querySelector('#modalCancelar button[autofocus]').focus();
                                            }, 0);
                                        }

                                        function cerrarModalCancelar() {
                                            document.getElementById('modalCancelar').style.display = 'none';
                                        }
                                    </script>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>

    <div class="content">                       
        <?php if(empty($resultado)){ echo ('<h4> ¡No existen registros! </h4>'); }
        else{ ?>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">PICKING INGRESO DE PRODUCTOS</strong>
                    </div>
                    <div class="card-body">
                        <table id="bootstrap-data-table" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                            <thead>
                                <tr>
                                    <th>PICAR</th>
                                    <th>ODC</th>
                                    <th>CÓDIGO DE BARRAS</th>
                                    <th>TOTAL ODC</th>
                                    <th>TOTAL ST</th>
                                    <th>SOBRANTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($resultado as $f){ ?>
                                    <tr>
                                        <td><center>
                                            <form method="POST" action="pick_ingreso.php">
                                                <input type="hidden" name="CodeBars" value="<?php echo $f['CodeBars']; ?>">
                                                <input type="hidden" name="cantidad_restante" value="<?php echo $f['cantidad_restante']; ?>">
                                                <button type="submit" class="btn btn-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                        <td><?php echo $f['odc']; ?></td>
                                        <td><?php echo $f['CodeBars']; ?></td>
                                        <td><?php echo $f['cantidad_odc']; ?></td>
                                        <td><?php echo $f['cantidad_st']; ?> </td>
                                        <td><?php echo $f['cantidad_restante']; ?> </td>
                                    </tr>                            
                                <?php } ?>   
                            </tbody>
                        </table>                                                                
                    </div>
                </div>
            </div>
        <?php } ?>           
    </div>

<?php include_once "footer.php"; ?>