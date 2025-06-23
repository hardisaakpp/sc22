<?php
    include_once "header.php";
    unset($_SESSION['odc']);
    unset($_SESSION['sts']);
    unset($_SESSION['st']);
    //if($userAdmin==5 || $userAdmin==1){ echo ('<h4> NO TIENE ACCESO</h4>'); }
    //else{
    if(isset($_SESSION['r']) || !empty($_SESSION['r'])){ $r=$_SESSION['r']; }
    else{
        include_once "../cx/bd_scs.php";
        $q="SELECT DISTINCT c.IdEmpresa,c.DocEntry,c.DocNum,CONVERT(varchar, c.CreateDate, 23) AS CreateDate,CardName,d.WhsCode,c.Comments
            FROM BISTAGING.dbo.STG_OrdenCompraCabecera AS c
            INNER JOIN (SELECT d.WhsCode,d.DocEntry FROM BISTAGING.dbo.STG_OrdenCompraDetalle AS d) AS d ON d.DocEntry=c.DocEntry
            WHERE c.CANCELED='N' AND c.DocStatus='O'
            ORDER BY c.DocEntry DESC,CreateDate DESC";
        $r=resp_simdim($q); 
        $_SESSION['r']=$r;
    }
?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>ORDENES DE COMPRA - ODC</h1>
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
                                            <button class="btn btn-danger" onclick="window.location.href='wllcm.php'">Sí, cancelar</button>
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
                <!-- Modal de Búsqueda de ODC -->
                <div class="modal fade" id="modalBuscarODC" tabindex="-1" role="dialog" aria-labelledby="modalBuscarODCLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalBuscarODCLabel">Buscar Orden de Compra</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="formBuscarODC">
                                    <div class="form-group">
                                        <label for="buscarODC">Número de Documento</label>
                                        <input type="text" class="form-control" id="buscarODC" name="buscarODC" placeholder="Ingrese el número de documento">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </form>
                                <div id="resultadoBusqueda"></div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <!-- IZQUIERDA: ORDENES DE COMPRA ACTIVAS -->
            <div class="col-12 col-md-6 mb-4">
                <!-- ORDENES DE COMPRA ACTIVAS (copia lo que tienes ahí) -->
                <?php if(empty($r)){ echo ('<h4> ¡No existen registros! </h4>'); }
                else{ ?>
                    <form method="post" action="buscar_odc.php" class="w-100">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">ACTIVAS</strong>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="bootstrap-data-table-left" class="table table-sm table-bordered table-hover" style="font-size: 0.7rem; width: 100%;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ELEGIR</th>
                                                <th>ODC</th>
                                                <th>FECHA</th>
                                                <th>NOMBRE</th> 
                                                <th>WHSCODE</th>
                                                <th>COMENTARIO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($r as $f){ ?>
                                                <tr>
                                                    <td>
                                                        <center>
                                                            <button type="button" class="btn btn-success btn-move" data-toggle="tooltip" title="Mover">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                                </svg>
                                                            </button>
                                                        </center>
                                                    </td>
                                                    <td><?php echo $f['DocNum']; ?></td>
                                                    <td><?php echo $f['CreateDate']; ?> </td>
                                                    <td><?php echo $f['CardName']; ?> </td>
                                                    <td><?php echo $f['WhsCode']; ?></td>
                                                    <td><?php echo $f['Comments']; ?></td>
                                                </tr>                            
                                            <?php } ?>   
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php } ?> 
            </div>

            <!-- DERECHA: ASIGNACIONES TEMPORALES -->
            <div class="col-12 col-md-6 mb-4">
                <?php if(empty($r)){ echo ('<h4> ¡No existen registros! </h4>'); }
                else{ ?>
                    <form method="post" action="buscar_st.php" id="formAsignados">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong class="card-title">ASIGNADAS</strong>
                                <div>
                                    <button id="btn-move-all-left" type="button" class="btn btn-sm btn-primary" title="Regresar todas">
                                        &larr; Regresar todas
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-success" id="btnEnviarAsignados">Enviar asignados</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="bootstrap-data-table-right" class="table table-sm table-bordered table-hover" style="font-size: 0.7rem; width: 100%;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ELEGIR</th>
                                                <th>ODC</th>
                                                <th>FECHA</th>
                                                <th>NOMBRE</th> 
                                                <th>WHSCODE</th>
                                                <th>COMENTARIO</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Input oculto que enviará los datos -->
                        <input type="hidden" name="asignados_json" id="asignados_json">
                    </form>
                <?php } ?> 
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var tableLeft = $('#bootstrap-data-table-left').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });

            var tableRight = $('#bootstrap-data-table-right').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });

            // Botón mover para tabla izquierda (botón verde, flecha a la derecha)
            function createMoveButtonLeft() {
                return `<center><button type="button" class="btn btn-success btn-move" data-toggle="tooltip" title="Mover a la derecha">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                    </svg>
                </button></center>`;
            }

            // Botón mover para tabla derecha (botón rojo, flecha a la izquierda)
            function createMoveButtonRight() {
                return `<center><button type="button" class="btn btn-danger btn-move" data-toggle="tooltip" title="Mover a la izquierda">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                        <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                    </svg>
                </button></center>`;
            }

            function actualizarBotonEnviar() {
                var totalFilas = tableRight.rows().count();
                if (totalFilas === 0) {
                    $('#btnEnviarAsignados').prop('disabled', true);
                } else {
                    $('#btnEnviarAsignados').prop('disabled', false);
                }
            }

            // Llamar esta función cada vez que se agregue o quite fila
            actualizarBotonEnviar(); // al cargar

            // Mover fila de izquierda a derecha
            $('#bootstrap-data-table-left tbody').on('click', 'button.btn-move', function() {
                var tr = $(this).closest('tr');
                var row = tableLeft.row(tr);
                var data = row.data();

                data[0] = createMoveButtonRight();

                tableRight.row.add(data).draw();
                row.remove().draw();

                actualizarBotonEnviar();
            });

            // Mover fila de derecha a izquierda
            $('#bootstrap-data-table-right tbody').on('click', 'button.btn-move', function() {
                var tr = $(this).closest('tr');
                var row = tableRight.row(tr);
                var data = row.data();

                data[0] = createMoveButtonLeft();

                tableLeft.row.add(data).draw();
                row.remove().draw();

                actualizarBotonEnviar();
            });

            // Mover todas a la izquierda
            $('#btn-move-all-left').on('click', function() {
                var allRows = tableRight.rows().data().toArray();

                allRows.forEach(function(data) {
                    data[0] = createMoveButtonLeft();
                    tableLeft.row.add(data);
                });

                tableLeft.draw();
                tableRight.clear().draw();

                actualizarBotonEnviar();
            });

            // Enviar asignados a tabla izquierda
            $('#formAsignados').on('submit', function(e) {
                var data = tableRight.rows().data().toArray();

                if (data.length === 0) {
                    e.preventDefault(); // No envía el formulario
                    alert('No hay asignaciones para enviar.');
                    return;
                }

                var asignados = data.map(function(row) {
                    return {
                        DocNum: row[1],
                        CreateDate: row[2],
                        CardName: row[3],
                        WhsCode: row[4],
                        Comments: row[5]
                    };
                });
                $('#asignados_json').val(JSON.stringify(asignados));
            });
        });
    </script>

<?php include_once "footer.php"; ?>
