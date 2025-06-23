<?php
    include_once "header.php";
    if (!isset($_SESSION['pick_in']) || empty($_SESSION['pick_in'])) { exit(); }
    $barcode = trim($_POST['CodeBars']);
    $cantidad_restante = $_POST['cantidad_restante'];
    include_once "../cx/bd_scs.php";
    $q="SELECT c.DocEntry,d.CodeBars,c.Comments,d.WhsCode,d.Dscription
        FROM BISTAGING.dbo.STG_OrdenCompraCabecera AS c
        INNER JOIN BISTAGING.dbo.STG_OrdenCompraDetalle AS d ON d.DocEntry = c.DocEntry
        WHERE c.CANCELED = 'N' AND c.DocStatus = 'O' AND d.CodeBars='$barcode'";
    $r=resp_onedim($q); 
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
                            <h1>PICKING PRODUCTO-UBICACIÓN</h1>
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
                                            <button class="btn btn-danger" onclick="window.location.href='odt_st_bod.php'">Sí, cancelar</button>
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
        <div class="row">
            <!-- Columna izquierda (formulario) -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <strong class="card-title">ASIGNAR UBICACIÓN A PRODUCTO</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Producto (Código de barras):</strong> <?= $barcode ?></p>
                        <p><strong>Producto (Nombre):</strong> <?= $r['Dscription'] ?></p>
                        <p><strong>Cantidad total por ubicar:</strong> <?= $cantidad_restante ?></p>

                        <div class="form-group">
                            <label for="comprobarCodigo"><strong>Escanear código de barras del producto para verificar:</strong></label>
                            <input type="text" id="comprobarCodigo" class="form-control" placeholder="Escanee el producto..." onkeypress="verificarCodigo(event)">
                            <div id="mensajeVerificacion" class="mt-2"></div>
                        </div>

                        <div class="form-group">
                            <label for="cantidadInput"><strong>Cantidad para esta ubicación:</strong></label>
                            <input type="number" id="cantidadInput" class="form-control" value="1" min="1" onkeypress="focoUbicacion(event)">
                        </div>

                        <div class="form-group">
                            <label for="ubicacionInput"><strong>Ubicación:</strong></label>
                            <input type="text" id="ubicacionInput" class="form-control" placeholder="Escanee ubicación y presione Enter" onkeypress="handleUbicacionEnter(event)" autofocus>
                        </div>

                        <div id="mensajeError" class="alert alert-warning" style="display: none;"></div>

                        <p class="mt-3"><strong>Total asignado:</strong> <span id="totalAsignado">0</span> / <?= $cantidad_restante ?></p>
                    </div>
                </div>
            </div>

            <!-- Columna derecha (tabla) -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <strong class="card-title">ASIGNACIONES TEMPORALES</strong>
                    </div>
                    <div class="card-body">
                        <form id="formAsignaciones" method="POST" action="p_asig_pick_in.php">
                            <input type="hidden" name="barcode" value="<?= $barcode ?>">
                            <table id="tablaAsignaciones" class="table table-striped table-bordered" style="font-size: 0.7rem;">
                                <thead>
                                    <tr>
                                        <th>Ubicación</th>
                                        <th>Cantidad</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaTemp"></tbody>
                            </table>
                            <button type="button" class="btn btn-danger" onclick="reiniciarTodo()">Eliminar Todo</button>
                            <button type="submit" class="btn btn-primary float-right" onclick="return prepararEnvio()">Enviar asignaciones</button>
                            <input type="hidden" name="asignaciones_json" id="asignacionesJson">
                            <input type="hidden" name="DocEntry" value="<?php echo $r['DocEntry']; ?>">
                            <input type="hidden" name="Comments" value="<?php echo $r['Comments']; ?>">
                            <input type="hidden" name="WhsCode" value="<?php echo $r['WhsCode']; ?>">
                            <input type="hidden" name="DocEntry" value="<?php echo $r['DocEntry']; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const codigoFijo = "<?= $barcode ?>";
        const cantidadMax = <?= $cantidad_restante ?>;
        let totalAsignado = 0;

        function handleUbicacionEnter(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Previene el envío del formulario

                const ubicacion = document.getElementById("ubicacionInput").value.trim();
                const cantidad = parseInt(document.getElementById("cantidadInput").value);
                const mensaje = document.getElementById("mensajeError");

                // 🔴 Validación: escanearon el código del producto como si fuera ubicación
                if (ubicacion === codigoFijo) {
                    playSound();
                    mensaje.innerHTML = `<strong>❌ Error:</strong> Escaneaste el código del producto en el campo de ubicación.`;
                    mensaje.style.display = "block";
                    document.getElementById("ubicacionInput").value = "";
                    return;
                }

                if (!ubicacion || isNaN(cantidad) || cantidad <= 0) {
                    mensaje.textContent = "Debe ingresar una ubicación válida y una cantidad mayor que 0.";
                    mensaje.style.display = "block";
                    return;
                }

                if (totalAsignado + cantidad > cantidadMax) {
                    playSound();
                    mensaje.innerHTML = `<strong>⚠️ Límite excedido:</strong> No puede asignar más de <strong>${cantidadMax}</strong> unidades en total.`;
                    mensaje.style.display = "block";
                    return;
                }

                // Ocultar mensaje y agregar la fila
                mensaje.style.display = "none";
                totalAsignado += cantidad;
                agregarFila(codigoFijo, ubicacion, cantidad);
                actualizarContador();
                limpiarInputs();

                if (totalAsignado === cantidadMax) {
                    document.getElementById("ubicacionInput").disabled = true;
                    document.getElementById("cantidadInput").disabled = true;
                }
            }
        }

        function agregarFila(codigo, ubicacion, cantidad) {
            const rowNode = tablaDT.row.add([
                `${ubicacion}<input type="hidden" name="ubicaciones[]" value="${ubicacion}">
                <input type="hidden" name="cantidades[]" value="${cantidad}">
                <input type="hidden" name="codigos[]" value="${codigo}">`,
                cantidad,
                `<td>
                    <button class="btn btn-sm btn-danger" onclick="eliminarFila(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 
                            0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 
                            0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 
                            5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                        </svg>
                    </button>
                </td>`
            ]).draw(false).node();

            rowNode.setAttribute("data-cantidad", cantidad); // Guarda la cantidad para restar al eliminar
        }
        
        function eliminarFila(btn) {
            const row = $(btn).closest('tr');
            const cantidad = parseInt(row.attr("data-cantidad"));
            totalAsignado -= cantidad;
            if (totalAsignado < 0) totalAsignado = 0;

            tablaDT.row(row).remove().draw();
            actualizarContador();

            document.getElementById("ubicacionInput").disabled = false;
            document.getElementById("cantidadInput").disabled = false;
        }

        function reiniciarTodo() {
            tablaDT.clear().draw();
            totalAsignado = 0;
            actualizarContador();
            document.getElementById("ubicacionInput").disabled = false;
            document.getElementById("cantidadInput").disabled = false;
        }

        function actualizarContador() {
            document.getElementById("totalAsignado").textContent = totalAsignado;
        }

        function limpiarInputs() {
            document.getElementById("ubicacionInput").value = "";
            document.getElementById("cantidadInput").value = 1;
            document.getElementById("ubicacionInput").focus();
        }

        function playSound() {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(440, ctx.currentTime);
            osc.connect(ctx.destination);
            osc.start();
            setTimeout(() => osc.stop(), 800);
        }

        function prepararEnvio() {
            const data = tablaDT.rows().data().toArray();
            if (data.length === 0) {
                alert("Debe asignar al menos una ubicación antes de enviar.");
                return false;
            }

            let asignaciones = [];

            data.forEach(row => {
                // Extrae la ubicación y cantidad del primer y segundo campo (como fueron agregados)
                const parser = new DOMParser();
                const doc = parser.parseFromString(row[0], 'text/html');
                const inputUbicacion = doc.querySelector('input[name="ubicaciones[]"]');
                const inputCantidad = doc.querySelector('input[name="cantidades[]"]');

                if (inputUbicacion && inputCantidad) {
                    asignaciones.push({
                        ubicacion: inputUbicacion.value,
                        cantidad: parseInt(inputCantidad.value)
                    });
                }
            });

            document.getElementById("asignacionesJson").value = JSON.stringify(asignaciones);
            return true;
        }

        function focoUbicacion(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("ubicacionInput").focus();
            }
        }

        function verificarCodigo(event) {
            if (event.key === "Enter") {
                const codigoEscaneado = document.getElementById("comprobarCodigo").value.trim();
                const mensaje = document.getElementById("mensajeVerificacion");

                if (!codigoEscaneado) return;

                if (codigoEscaneado === codigoFijo) {
                    mensaje.innerHTML = `<div class="alert alert-success"><strong>✅ Producto verificado:</strong> El código escaneado coincide con el producto.</div>`;
                } else {
                    playSound();
                    mensaje.innerHTML = `<div class="alert alert-danger"><strong>❌ Código incorrecto:</strong> El producto escaneado <strong>${codigoEscaneado}</strong> no coincide con el código esperado <strong>${codigoFijo}</strong>.</div>`;
                }

                // Limpiar campo y mantener foco
                document.getElementById("comprobarCodigo").value = "";
                document.getElementById("comprobarCodigo").focus();
            }
        }

        let tablaDT;
        document.addEventListener("DOMContentLoaded", function () {
            tablaDT = $('#tablaAsignaciones').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                paging: false,
                info: false,
                searching: true,
                initComplete: function () {
                    // Esperar a que se renderice el campo search y luego agregar el botón
                    const searchInput = $('#tablaAsignaciones_filter label');
                    const clearButton = $('<button class="btn btn-sm btn-outline-secondary ml-2" type="button">🧹</button>');

                    clearButton.on('click', function () {
                        tablaDT.search('').draw(); // Limpiar búsqueda
                    });

                    searchInput.append(clearButton);
                }
            });
        });
    </script>

<?php include_once "footer.php"; ?>
