<?php session_start(); if(!isset($_SESSION['username']) || !isset($_SESSION['perfil'])){ session_unset(); session_destroy(); header('location:index.php'); exit(); } ?>
<?php
    include_once "header.php";
    if($userAdmin<>1){ echo ('ACCESO DENEGADO'); exit(); } //si no es admin no abre
    else{
        $q="SELECT a.id AS 'WhsCode', articulosContar AS 'Quantity', a.nombre, tcd.tomaCode
        FROM users u JOIN Almacen a ON u.fk_ID_almacen_invs=a.id
        LEFT JOIN (SELECT FK_ID_almacen, tomaCode FROM StockCab WHERE tipo='TF' AND convert(VARCHAR(10), [date], 102) = convert(VARCHAR(10), getdate(), 102)) tcd ON a.id=tcd.FK_ID_almacen WHERE realizaConteo=1;";
        $r=resp_simdim($q); ?>
        <div class="breadcrumbs bg-body text-body dark:bg-dark dark:text-light py-1">
            <div class="container-fluid">
                <div class="row m-0">
                    <div class="col-sm-6">
                        <h4>TOMAS FISICAS ALEATORIAS REALIZADAS</h4>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <button type="button" class="btn btn-outline-success" onclick="chargeTFA();"><i class="bi bi-play"></i></button>
                        <button type="button" class="btn btn-outline-warning" onclick="location.reload();"><i class="bi bi-arrow-clockwise"></i></button>
                        <button type="button" class="btn btn-outline-warning" onclick="location.reload();"><i class="bi bi-envelope"></i></button>
                        <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <script>
                function chargeTFA(){
                <?php foreach($r as $f){ ?>  
                        createTFA('<?php echo $f['WhsCode'] ?>', '<?php echo $f['Quantity'] ?>');
                    <?php } ?>
                }
                function createTFA(WhsCode, Quantity){    
                    var parametros = 
                        {
                            "WhsCode" : WhsCode ,
                            "Quantity" : Quantity
                        };
                        $.ajax({
                            data: parametros,
                            url: 'php/loadTomaFisicaAleatoria.php',
                            type: 'POST',
                            //    timeout: 3000,
                            success: function(data){
                                //console.log(data);
                                es=document.getElementById("tc"+WhsCode );
                                es.innerText = '✔️';
                            },
                            error: function(){
                            console.log('error de conexion - revisa tu red');
                            }
                        });
                }
            </script>

            <div class="card">
                <div class="card-header">
                    <strong class="card-title" align="center">BODEGAS ASIGNADAS PARA CONTEO</strong>
                </div>
                <div class="container">
                    <div class="table-responsive">
                        <table id="employee_data" class="table table-striped table-bordered nowrap" style="width:100%" style="font-size: 0.8rem;">
                            <thead align="left" style="background: #a6acaf">
                                <tr width="100%" align="center" style="font-size: 0.9rem;">
                                    <th>Bodega</th>
                                    <th>Cantidad</th>
                                    <th>Actual</th>                                
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($r as $f){ ?>
                                <tr align="center" style="font-size: 0.8rem;"> 
                                    <td align="left"><?php echo $f['nombre'] ?></td>
                                    <td><?php echo $f['Quantity'] ?></td>
                                    <td id='<?php echo "tc".$f['WhsCode']  ?>'><?php echo $f['tomaCode']  ?></td>
                                </tr>
                            <?php } ?>   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>   
    <?php }; ?>
<?php include_once "footer.php"; ?>