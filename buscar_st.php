<?php
    session_start();
    if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['asignados_json']!='')){ if(isset($_POST['asignados_json'])){ $_SESSION['odc'] = json_decode($_POST['asignados_json'], true); }else{ echo "No se recibió información de asignados.";  exit;} }
    include_once "header.php";
    //if($userAdmin==5 || $userAdmin==1){ echo ('<h4> NO TIENE ACCESO</h4>'); }
    //else{
    if(isset($_SESSION['st']) || !empty($_SESSION['st'])){ $st=$_SESSION['st']; }
    else{
        include_once "../cx/bd_scs.php";
        $q="SELECT TOP 100 c.DocDate,c.DocNum,c.Filler,c.DocNum_Tr,c.ToWhsCode,CONVERT(varchar, c.LineStatus, 23) AS LineStatus,c.Comments,a.fk_emp,a.nombre
                FROM STORECONTROL.dbo.SotCab_MT AS c 
                INNER JOIN STORECONTROL.dbo.Almacen AS a ON a.cod_almacen=c.ToWHSCode
                ORDER BY c.DocDate DESC";
        $st=resp_simdim($q); 
        $_SESSION['st']=$st;
    }
?>
        <style>
            .toast-copiado {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 12px 18px;
                border-radius: 5px;
                font-size: 0.9rem;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
                z-index: 1050;
                opacity: 0.95;
            }
        </style>
        <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>SOLICITUDES DE TRANSFERENCIA - ST</h1>
                            </div>
                        </div>
                    </div>
                    <?php
                        //lote opción principal
                        if($_SERVER['REQUEST_METHOD']==='POST'){
                            unset($_POST['bootstrap-data-table_length']); 
                            unset($_POST['bootstrap-data-table2_length']); 
                            unset($_POST['bootstrap-data-table3_length']);
                            unset($_POST['bootstrap-data-table4_length']); 
                            unset($_POST['bootstrap-data-table5_length']); 
                            unset($_POST['bootstrap-data-table6_length']); 
                            if(!isset($_SESSION['sts'])){ $_SESSION['sts'] = []; }
                            $_SESSION['sts']+=$_POST;
                            $_POST=[];
                        }
                        //separación de arrays para seleccionados y sobrantes
                        if(isset($_SESSION['sts']) || !empty($_SESSION['sts'])){
                            foreach ($st as $f) {
                                $c = $f['DocNum'];
                                $o[$c] = $f;
                            }                    
                            $k1=array_keys($o);
                            $k2=array_keys($_SESSION['sts']);
                            $k_diferentes=array_diff($k1, $k2);
                            $st=[];
                            foreach($k_diferentes as $k){ $st[$k]=$o[$k]; }
                            $k_comunes=array_intersect($k1, $k2);
                            $c=[];
                            foreach($k_comunes as $k){ $c[$k]=$o[$k]; }
                        }
                    ?>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTabla5">ODCs con STs</button>
                                        <div class="modal fade" id="modalTabla5" tabindex="-1" role="dialog" aria-labelledby="tituloTabla" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
                                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                                        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
                                                        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
                                                        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
                                                        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                                                        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
                                                        <form method="post" action="odt_st_bod.php">            
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <strong class="card-title">STs RELACIONADAS CON ODCs</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="table-responsive">
                                                                            <table id="bootstrap-data-table6" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                                                                <thead>
                                                                                    <tr>   
                                                                                        <th>ELIMINAR</th>                                                                                  
                                                                                        <th>NUMERO DOCUMENTO</th>
                                                                                        <th>TIENDA</th>
                                                                                        <th>COMENTARIO ST</th>
                                                                                        <th>ODC RELACIONADO</th>   
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php if(isset($c)){ foreach($c as $k=>$f){ ?>
                                                                                        <tr>
                                                                                            <td><center><button type="button" class="btn btn-danger" onclick="eliminarST('<?php echo $k; ?>', this)">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                                                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                                                                                </svg></button>
                                                                                            </td>
                                                                                            <td><?php echo $f['DocNum']; ?></td>
                                                                                            <td><?php echo $f['nombre']; ?></td>
                                                                                            <td><?php echo $f['Comments']; ?></td>
                                                                                            <?php if(isset($f['odc'])){ ?>
                                                                                                <td><?php echo $f['odc']; ?></td>
                                                                                            <?php }else{ ?>
                                                                                                <td><select class="btn btn-primary" name="<?php echo $f['DocNum']; ?>" required>
                                                                                                    <option value="">Buscar</option>
                                                                                                    <?php foreach($_SESSION['odc'] as $k1=>$f1){ echo "<option value='$f1[DocNum]'>$f1[DocNum]</option>"; } ?>
                                                                                                </select></td>
                                                                                            <?php } ?>
                                                                                        </tr>                            
                                                                                    <?php } } ?>   
                                                                                </tbody>
                                                                            </table>  
                                                                        </div>                                                              
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="alert alert-error text-center"> 
                                                                <input type="hidden" name="sts_odcs" value="<?php echo htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input class="btn btn-success dropdown-toggle" type="submit" value="FINALIZAR BÚSQUEDA ST" />
                                                                <button type="button" class="btn btn-outline-danger" onclick="eliminarTodoST()">ELIMINAR TODO</button>
                                                            </div>
                                                        </form>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#bootstrap-data-table6').DataTable({
                                                                    "language": {
                                                                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                                                                    },
                                                                    "order": [[ 1, "desc" ]], // Orden por número de documento
                                                                    "pageLength": 10
                                                                });
                                                            });
                                                        </script>
                                                        <script>
                                                            function eliminarST(clave, boton) {
                                                                if (!confirm('¿Deseas eliminar esta ST?')) return;

                                                                fetch('eliminar_st_ajax.php', {
                                                                    method: 'POST',
                                                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                                    body: 'clave=' + encodeURIComponent(clave)
                                                                })
                                                                .then(response => response.json())
                                                                .then(data => {
                                                                    if (data.success) {
                                                                        const fila = boton.closest('tr');
                                                                        fila.remove();
                                                                        window.location.replace(window.location.pathname);
                                                                    } else {
                                                                        alert('No se pudo eliminar.');
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error('Error al eliminar:', error);
                                                                    alert('Error del servidor.');
                                                                });
                                                            }
                                                        </script>
                                                        <script>
                                                            function eliminarTodoST() {
                                                                if (!confirm("¿Deseas eliminar todas las STs seleccionadas?")) return;
                                                                fetch('eliminar_todo_st.php', {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type': 'application/x-www-form-urlencoded'
                                                                    },
                                                                    body: 'accion=eliminar'
                                                                })
                                                                .then(response => response.json())
                                                                .then(data => {
                                                                    if (data.success) {
                                                                        window.location.replace(window.location.pathname);
                                                                    } else {
                                                                        alert("No se pudo eliminar.");
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error("Error al eliminar:", error);
                                                                });
                                                            }
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalTabla">STs Seleccionadas</button>
                                        <div class="modal fade" id="modalTabla" tabindex="-1" role="dialog" aria-labelledby="tituloTabla" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <form method="post" action="buscar_st.php">            
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <strong class="card-title">STs SELECCIONADAS</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="table-responsive">
                                                                            <table id="bootstrap-data-table2" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                                                                <thead>
                                                                                    <tr>   
                                                                                        <th>ELIMINAR</th>                                                                                  
                                                                                        <th>NUMERO DOCUMENTO</th>
                                                                                        <th>TIENDA</th>
                                                                                        <th>ESTATUS</th>
                                                                                        <th>FECHA CREACIÓN</th>
                                                                                        <th>COMENTARIO</th>                                                                                         
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php if(isset($c)){ foreach($c as $k=>$f){ ?>
                                                                                        <tr>
                                                                                            <td><center><button type="button" class="btn btn-danger" onclick="eliminarST('<?php echo $k; ?>', this)">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                                                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                                                                                </svg></button>
                                                                                            </td>
                                                                                            <td><?php echo $f['DocNum']; ?></td>
                                                                                            <td><?php echo $f['nombre']; ?></td>
                                                                                            <td><?php echo $f['LineStatus']; ?></td>
                                                                                            <td><?php echo $f['DocDate']; ?> </td>
                                                                                            <td><?php echo $f['Comments']; ?> </td>
                                                                                        </tr>                            
                                                                                    <?php } } ?>   
                                                                                </tbody>
                                                                            </table>  
                                                                        </div>                                                              
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="alert alert-error" align="center"> 
                                                                <button type="button" class="btn btn-outline-danger" onclick="eliminarTodoST()">ELIMINAR TODO</button>
                                                            </div>
                                                        </form>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#bootstrap-data-table2').DataTable({
                                                                    "language": {
                                                                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                                                                    },
                                                                    "order": [[ 1, "desc" ]], // Orden por número de documento
                                                                    "pageLength": 10
                                                                });
                                                            });
                                                        </script>
                                                        <script>
                                                            function eliminarST(clave, boton) {
                                                                if (!confirm('¿Deseas eliminar esta ST?')) return;

                                                                fetch('eliminar_st_ajax.php', {
                                                                    method: 'POST',
                                                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                                    body: 'clave=' + encodeURIComponent(clave)
                                                                })
                                                                .then(response => response.json())
                                                                .then(data => {
                                                                    if (data.success) {
                                                                        const fila = boton.closest('tr');
                                                                        fila.remove();
                                                                        window.location.replace(window.location.pathname);
                                                                    } else {
                                                                        alert('No se pudo eliminar.');
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error('Error al eliminar:', error);
                                                                    alert('Error del servidor.');
                                                                });
                                                            }
                                                        </script>
                                                        <script>
                                                            function eliminarTodoST() {
                                                                if (!confirm("¿Deseas eliminar todas las STs seleccionadas?")) return;
                                                                fetch('eliminar_todo_st.php', {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type': 'application/x-www-form-urlencoded'
                                                                    },
                                                                    body: 'accion=eliminar'
                                                                })
                                                                .then(response => response.json())
                                                                .then(data => {
                                                                    if (data.success) {
                                                                        window.location.replace(window.location.pathname);
                                                                    } else {
                                                                        alert("No se pudo eliminar.");
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error("Error al eliminar:", error);
                                                                });
                                                            }
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalTabla2">ODCs</button>
                                        <div class="modal fade" id="modalTabla2" tabindex="-1" role="dialog" aria-labelledby="tituloTabla" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <form method="post" action="buscar_st.php">            
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <strong class="card-title">ODCs SELECCIONADAS</strong>
                                                                        <p>Haga click en cualquier número de Documento ODC para copiar el número</p>
                                                                    </div>
                                                                    <div class="card-body text-center">
                                                                        <div class="table-responsive">
                                                                            <table id="bootstrap-data-table4" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                                                                <thead>
                                                                                    <tr>                                                                                    
                                                                                        <th>NUMERO DOCUMENTO</th>
                                                                                        <th>FECHA CREACIÓN</th>
                                                                                        <th>WHSCODE</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php if(isset($c)){ foreach($_SESSION['odc'] as $k=>$f){ ?>
                                                                                        <tr>
                                                                                            <td class="copiar-odc" style="cursor: pointer;"><?php echo $f['DocNum']; ?></td>
                                                                                            <td><?php echo $f['CreateDate']; ?> </td>
                                                                                            <td><?php echo $f['WhsCode']; ?></td>
                                                                                        </tr>                            
                                                                                    <?php } } ?>   
                                                                                </tbody>
                                                                            </table> 
                                                                        </div>                                                               
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#bootstrap-data-table4').DataTable({
                                                                    "language": {
                                                                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                                                                    },
                                                                    "order": [[ 1, "desc" ]], // Orden por número de documento
                                                                    "pageLength": 10
                                                                });
                                                            });

                                                            document.addEventListener("DOMContentLoaded", function () {
                                                                // Clic simple para copiar
                                                                document.querySelectorAll('.copiar-odc').forEach(function (celda) {
                                                                    celda.addEventListener('click', function () {
                                                                        const texto = this.textContent.trim();
                                                                        copiarAlPortapapeles(texto);
                                                                        mostrarToast(`Número de ODC "${texto}" copiado al portapapeles.`);
                                                                    });

                                                                    // Presión larga (opcional)
                                                                    let presionado;
                                                                    celda.addEventListener('mousedown', function () {
                                                                        presionado = setTimeout(() => {
                                                                            const texto = this.textContent.trim();
                                                                            copiarAlPortapapeles(texto);
                                                                            mostrarToast(`Número de ODC "${texto}" copiado al portapapeles.`);
                                                                        }, 700); // 700ms para considerar "presión larga"
                                                                    });

                                                                    celda.addEventListener('mouseup', function () {
                                                                        clearTimeout(presionado);
                                                                    });

                                                                    celda.addEventListener('mouseleave', function () {
                                                                        clearTimeout(presionado);
                                                                    });
                                                                });

                                                                function copiarAlPortapapeles(texto) {
                                                                    navigator.clipboard.writeText(texto).catch(err => {
                                                                        console.error("Error al copiar:", err);
                                                                    });
                                                                }

                                                                function mostrarToast(mensaje) {
                                                                    const toast = document.createElement('div');
                                                                    toast.textContent = mensaje;
                                                                    toast.className = 'toast-copiado';
                                                                    document.body.appendChild(toast);
                                                                    setTimeout(() => {
                                                                        toast.remove();
                                                                    }, 2500);
                                                                }
                                                            });
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#modalTabla1" data-toggle="tooltip" title="Buscar otro ST">🔎</button>
                                        <div class="modal fade" id="modalTabla1" tabindex="-1" role="dialog" aria-labelledby="tituloTabla" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <input type="text" id="buscarST" class="form-control" placeholder="Ingrese número de ST o nombre..." style="margin-bottom:10px; width:50%; display:inline-block;">
                                                        <button type="button" class="btn btn-info" onclick="buscarST()">Buscar otra ST</button>
                                                        <form id="formAgregarSTs" method="post" action="agregar_st_form.php">            
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <strong class="card-title">STs ENCONTRADAS</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="table-responsive">
                                                                            <table id="bootstrap-data-table3" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>SELECCIÓN</th>
                                                                                        <th>NUMERO DOCUMENTO</th>
                                                                                        <th>TIENDA</th>
                                                                                        <th>ESTATUS</th>
                                                                                        <th>FECHA CREACIÓN</th>
                                                                                        <th>COMENTARIO</th>          
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody  id="tablaResultados">
                                                                                </tbody>
                                                                            </table> 
                                                                        </div>                                                               
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="alert alert-error" align="center">             
                                                                <input class="btn btn-success dropdown-toggle" type="submit" value="AGREGAR" />
                                                                <input class="btn btn-primary dropdown-toggle" type="reset" value="REESTABLECER" />
                                                            </div>
                                                        </form>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#bootstrap-data-table3').DataTable({
                                                                    "language": {
                                                                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                                                                    },
                                                                    "order": [[ 1, "desc" ]], // Orden por número de documento
                                                                    "pageLength": 10
                                                                });
                                                            });
                                                        </script>
                                                        <script>
                                                            function buscarST() {
                                                                const query = document.getElementById('buscarST').value;

                                                                $.ajax({
                                                                    url: 'buscar_st_ajax.php',
                                                                    method: 'POST',
                                                                    data: { query: query },
                                                                    dataType: 'json',
                                                                    success: function(respuesta) {
                                                                        let html = '';

                                                                        if (respuesta.length > 0) {
                                                                            respuesta.forEach(f => {
                                                                                const key = `${f.DocNum}`;
                                                                                html += `<tr>
                                                                                    <td><center><input type="checkbox" name="search[${key}][activo]" value="1" /></center></td>
                                                                                    <td>${f.DocNum}<input type="hidden" name="search[${key}][DocNum]" value="${f.DocNum}" /></td>
                                                                                    <td>${f.nombre}<input type="hidden" name="search[${key}][nombre]" value="${f.nombre}" /></td>
                                                                                    <td>${f.LineStatus}<input type="hidden" name="search[${key}][LineStatus]" value="${f.LineStatus}" /></td>
                                                                                    <td>${f.DocDate}<input type="hidden" name="search[${key}][DocDate]" value="${f.DocDate}" /></td>
                                                                                    <td>${f.Comments}<input type="hidden" name="search[${key}][Comments]" value="${f.Comments}" /></td>
                                                                                    <input type="hidden" name="search[${key}][IdEmpresa]" value="${f.DocNum}" />
                                                                                </tr>`;
                                                                            });
                                                                        } else {
                                                                            html = '<tr><td colspan="5" align="center">No se encontraron resultados</td></tr>';
                                                                        }

                                                                        document.getElementById('tablaResultados').innerHTML = html;
                                                                    },
                                                                    error: function(xhr) {
                                                                        alert("Error al buscar STs");
                                                                        console.error(xhr.responseText);
                                                                    }
                                                                });
                                                            }
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalTabla3" data-toggle="tooltip" title="Buscar STs por ODC">🔦</button>
                                        <div class="modal fade" id="modalTabla3" tabindex="-1" role="dialog" aria-labelledby="tituloTabla" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <input type="text" id="buscarST_ODT" class="form-control" placeholder="Ingrese número de OCD" style="margin-bottom:10px; width:50%; display:inline-block;">
                                                        <button type="button" class="btn btn-info" onclick="buscarST_ODT()">Buscar STs por ODT</button>
                                                        <form id="formAgregarSTs" method="post" action="agregar_st_form.php">            
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                        <strong class="card-title">STs ENCONTRADAS</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="table-responsive">
                                                                            <table id="bootstrap-data-table5" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>SELECCIÓN</th>
                                                                                        <th>NUMERO DOCUMENTO</th>
                                                                                        <th>TIENDA</th>
                                                                                        <th>ESTATUS</th>
                                                                                        <th>FECHA CREACIÓN</th>
                                                                                        <th>COMENTARIO</th>          
                                                                                        <th>BUSQUEDA ODC</th> 
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody  id="tablaResultados2">
                                                                                </tbody>
                                                                            </table> 
                                                                        </div>                                                               
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="alert alert-error" align="center">             
                                                                <input class="btn btn-success dropdown-toggle" type="submit" value="AGREGAR" />
                                                                <button type="button" class="btn btn-warning" onclick="toggleSeleccion()">SELECCIONAR TODO</button>
                                                                <input class="btn btn-primary dropdown-toggle" type="reset" value="REESTABLECER" />                                                                
                                                            </div>
                                                        </form>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#bootstrap-data-table5').DataTable({
                                                                    "language": {
                                                                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                                                                    },
                                                                    "order": [[ 1, "desc" ]], // Orden por número de documento
                                                                    "pageLength": 10
                                                                });
                                                            });
                                                        </script>
                                                        <script>
                                                            function buscarST_ODT() {
                                                                const query = document.getElementById('buscarST_ODT').value;
                                                                $.ajax({
                                                                    url: 'buscar_st_odc_ajax.php',
                                                                    method: 'POST',
                                                                    data: { query: query },
                                                                    dataType: 'json',
                                                                    success: function(respuesta) {
                                                                        let html = '';
                                                                        if (respuesta.length > 0) {
                                                                            respuesta.forEach(f => {
                                                                                const key = `${f.DocNum}`;
                                                                                html += `<tr>
                                                                                    <td><center><input type="checkbox" name="search[${key}][activo]" value="1" /></center></td>
                                                                                    <td>${f.DocNum}<input type="hidden" name="search[${key}][DocNum]" value="${f.DocNum}" /></td>
                                                                                    <td>${f.nombre}<input type="hidden" name="search[${key}][nombre]" value="${f.nombre}" /></td>
                                                                                    <td>${f.LineStatus}<input type="hidden" name="search[${key}][LineStatus]" value="${f.LineStatus}" /></td>
                                                                                    <td>${f.DocDate}<input type="hidden" name="search[${key}][DocDate]" value="${f.DocDate}" /></td>
                                                                                    <td>${f.Comments}<input type="hidden" name="search[${key}][Comments]" value="${f.Comments}" /></td>
                                                                                    <td>${query}<input type="hidden" name="search[${key}][query]" value="${query}" /></td>
                                                                                    <input type="hidden" name="search[${key}][IdEmpresa]" value="${f.DocNum}" />
                                                                                </tr>`;
                                                                            });
                                                                        } else {
                                                                            html = '<tr><td colspan="5" align="center">No se encontraron resultados</td></tr>';
                                                                        }

                                                                        document.getElementById('tablaResultados2').innerHTML = html;
                                                                    },
                                                                    error: function(xhr) {
                                                                        alert("Error al buscar ODT");
                                                                        console.error(xhr.responseText);
                                                                    }
                                                                });
                                                            }
                                                        </script>
                                                        <script>
                                                            let todoSeleccionado = false;

                                                            function toggleSeleccion() {
                                                                const checkboxes = document.querySelectorAll('#tablaResultados2 input[type="checkbox"]');
                                                                checkboxes.forEach(cb => cb.checked = !todoSeleccionado);
                                                                todoSeleccionado = !todoSeleccionado;

                                                                // Cambiar el texto del botón según estado
                                                                const boton = event.target;
                                                                boton.textContent = todoSeleccionado ? "Deseleccionar todo" : "Seleccionar todo";
                                                            }
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
            <?php if(empty($st)){ echo ('<h4> ¡No existen registros! </h4>'); }
            else{ ?>
                <form method="post" action="buscar_st.php">            
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">PRIMERAS 100 SOLICITUDES DE TRANSFERENCIA ACTIVAS</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                    <thead>
                                        <tr>
                                            <th>SELECCIÓN</th>
                                            <th>NUMERO DOCUMENTO</th>
                                            <th>TIENDA</th>
                                            <th>ESTATUS</th>
                                            <th>FECHA CREACIÓN</th>
                                            <th>COMENTARIO</th>         
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($st as $f){ //c.Filler,c.DocNum_Tr,c.ToWhsCode,c.LineStatus,a.fk_emp?>
                                            <tr>
                                                <td><center><input type="checkbox" name="<?php echo $f['DocNum'];?>" value=""></td>
                                                <td><?php echo $f['DocNum']; ?></td>
                                                <td><?php echo $f['nombre']; ?></td>
                                                <td><?php echo $f['LineStatus']; ?></td>
                                                <td><?php echo $f['DocDate']; ?> </td>
                                                <td><?php echo $f['Comments']; ?> </td>
                                            </tr>                            
                                        <?php } ?>   
                                    </tbody>
                                </table>                                                                
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-error" align="center">             
                        <input class="btn btn-success dropdown-toggle" type="submit" value="AGREGAR" />
                        <input class="btn btn-info dropdown-toggle" type="reset" value="REESTABLECER" />
                    </div>
                </form>
            <?php } ?>           
        </div>
    <?php //} ?>


<?php include_once "footer.php"; ?>