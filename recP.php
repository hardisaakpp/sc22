<?php
    include_once "header.php";

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
    $s1 = $db->query("
             SELECT d.Id as ID, d.ItemCode, d.CantidadAbierta AS stock, d.Scan, 
                0 as [DocNum_Sot]        ,d.DocEntry ,
                d.Descripcion as descripcion, d.CodeBars AS codigoBarras
                ,0 as [fk_idgroup]
                    ,LineNum
                FROM [dbo].[TransferenciasCabecera] c
                JOIN TransferenciasDetalle d ON c.Id = d.id_TrCab
                WHERE c.id =".$idcab."  " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);

    $stmt = $db->prepare("SELECT CodeBars, datetime FROM TransferenciasDiferencias WHERE id_TrCab = ? ORDER BY datetime ASC");
    $stmt->execute([$idcab]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los almacenes desde sesión
    $whsCica = $_SESSION["whsCica"] ?? null;
    $whsTr = $_SESSION["whsTr"] ?? null;

    if (!$whsCica || !$whsTr) {
        exit("No se han definido los almacenes del usuario.");
    }

    // Obtener los códigos de almacén
    $almacenTran = $db->query("SELECT cod_almacen FROM almacen WHERE id = '{$whsTr}'")->fetchColumn();
    $almacenR = $db->query("SELECT cod_almacen FROM almacen WHERE id = '{$whsCica}'")->fetchColumn();
    $TEMPa1 = $db->query("SELECT CreadaTransferencia FROM TransferenciasCabecera WHERE id = '{$idcab}'")->fetchColumn();
    $NumTransferencia = $db->query("SELECT NumTransferencia FROM TransferenciasCabecera WHERE id = '{$idcab}'")->fetchColumn();
    $Responsable = $db->query("SELECT Responsable FROM TransferenciasCabecera WHERE id = '{$idcab}'")->fetchColumn();

            $Filler = $almacenTran;
            $ToWhsCode = $almacenR;
            $Enable = $TEMPa1;

?>

<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    .col-barcodes {
        font-size: 11px;
    } 
    .col-nombre {
        font-size: 10px;
    }
    .fila-completa {
        background-color: #d4edda !important; 
    }
    .fila-parcial {
        background-color: #e2e3e5 !important;
    }
</style>

<div class="content">

    <!-- Card Principal picking -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;gap:8px;flex-wrap:nowrap;min-width:0;overflow:hidden;">
                <strong>Recepción </strong>  basado en transferencia <?php echo $NumTransferencia.'. '.$Filler.' a '.$ToWhsCode ?> 
                <span id="warehouseCodeHeader"> </span>
            </div>
           
            <div class="card-body card-block">
                <form id="form-codigo">
                        <div class="input-group mb-3 flex-nowrap">
                            <div class="col-2"><label for="responsable" class="form-label"><b>Responsable:</b></label></div>
                            <div class="col-7">
                                <input type="text"  maxlength="49" id="responsable" name="responsable" class="form-control" value="<?= htmlspecialchars($Responsable ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="input-group mb-3 flex-nowrap">
                            <div class="col-2"><input type="text" placeholder="cantidad" id="txtu" class="form-control" id="cantidad" autocomplete="off" min="1" max="1000" value="1"></div>
                            <div class="col-7"><input type="text"  maxlength="20" id="codigo" placeholder="Cod. Barras" class="form-control" autocomplete="off" autofocus></div>
                            <div class="col-3">
                                <button id="btnAgregar" type="button"  class="btn btn-outline-primary btn-sm">➕</button>
                                <button id="btnGuardar" type="button"  class="btn btn-outline-success">💾</button>
                            </div>
                        </div>
                </form>
                        
                <div id="loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center; padding-top:20%;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p>Procesando, por favor espera...</p>
                </div>

                <table id="tabla">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Artículo</th>
                            <th>Solicitado</th>
                            <th>Escaneado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scans as $item): ?>
                            <tr data-codigo="<?= $item->codigoBarras ?>" data-id="<?= $item->ID ?>" data-itemcode="<?= $item->ItemCode ?>" data-linenum="<?= $item->LineNum ?>" data-id="<?= $item->ID ?>" data-docentry="<?= $item->DocEntry ?>" >
                                <td class="col-barcodes"><?= $item->codigoBarras ?></td>
                                <td class="col-nombre"><?= $item->ItemCode."- ".$item->descripcion ?></td>
                                <td><?= $item->stock ?></td>
                                <td class="escaneados"><?= $item->Scan ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                        </br>
                <div class="form-group" style="display:flex; gap:10px; justify-content:flex-end;">
                    <button id="btnTransferencia" class="btn btn-outline-success btn-sm">Crear Transferencia</button>
                    <button id="btnDescargarCSV" class="btn btn-outline-info btn-sm">📥 Exportar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificación -->
    <div id="alertaCodigo" class="alert alert-danger text-center" style="display:none; position:fixed; top:20px; right:20px; z-index:9999;">
        Código no encontrado
    </div>

    <!-- Card para codigos no reconocidos -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Códigos Adicionales
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered" id="tablaNoReconocidos">
                    <thead>
                        <tr>
                            <th>Código de barras</th>
                            <th>Fecha / Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se agregan dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  


</div>

<script>

    let reconteo = false; // Variable global para saber si ya hubo reconteo


    async function crearTransferencia() {
        
        if (!responsable) {
            alert("❌ Debe ingresar el Responsable antes de crear la transferencia.");
            mostrarLoader(false);
            return;
        }

        mostrarLoader(true); // Mostrar loader
        const filas = document.querySelectorAll('#tabla tbody tr');
        let errorCritico = false;
        const datos = [];

              let incompletos = 0;
            let totalEscaneado = 0;

            filas.forEach(fila => {
                const solicitado = parseInt(fila.cells[2].textContent);
                const escaneado = parseInt(fila.cells[3].textContent);
                if (escaneado > solicitado) {
                    errorCritico = true;
                }
                if (escaneado < solicitado) {
                    incompletos++;
                    diferencias = true;

                    // Primera vez que hay diferencias: resetear escaneado
                    if (!reconteo) {
                        fila.cells[3].textContent = 0;
                        fila.style.backgroundColor = ""; // dejar fondo blanco/sin color
                        fila.classList.remove("fila-completa", "fila-parcial"); // opcional limpiar clases de color
                    }
                }
                if (escaneado > 0) {
                    totalEscaneado++;
                }
            });

            // Primera vez que hay diferencias: mostrar alerta y detener
            if (diferencias && !reconteo) {
                alert("⚠️ ES NECESARIO RECONTAR LOS ITEMS CON DIFERENCIAS PARA PROCEDER CON LA CREACION DE TRASLADO.");
                reconteo = true; // activamos reconteo
                mostrarLoader(false);
                return; // detener creación hasta que se haga el reconteo
            }


            if (errorCritico) {
                alert("❌ Error crítico: Hay productos con escaneado mayor al solicitado. Contacte con sistemas.");
                    mostrarLoader(false);
                return;
            }
            if (totalEscaneado === 0) {
                alert("⚠️ No se ha escaneado ningún producto. No se puede crear la transferencia.");
                    mostrarLoader(false);
                return;
            }              

             // Generar JSON de transferencia
                const stockTransfer = {
                    comments: "sc22",
                    fromWarehouse: "<?= $Filler ?>",
                    toWarehouse: "<?= $ToWhsCode ?>",
                    priceList: -2,
                    stockTransferLines: []
                };

                filas.forEach(fila => {
                    const escaneado = parseInt(fila.cells[3].textContent);
                    if (escaneado > 0) {
                        const item = {
                            itemCode: fila.getAttribute('data-itemcode'),
                            quantity: escaneado,
                            warehouseCode: "<?= $ToWhsCode ?>",
                            baseEntry: parseInt(fila.getAttribute('data-docentry')),
                            baseLine: parseInt(fila.getAttribute('data-linenum')),
                            baseType: 67
                        };
                        stockTransfer.stockTransferLines.push(item);
                    }
                });

              
     // Crear transferencia 

                    let continuar = false;

                        if (incompletos === 0) {
                            // Todos completos, preguntar confirmación antes de continuar
                            mostrarLoader(false);
                            if (confirm("¿Está seguro de crear la transferencia?")) {
                                mostrarLoader(true);
                                continuar = true;
                            } else {
                                mostrarLoader(false);
                                alert("⚠️ Transferencia cancelada por el usuario.");
                                return;
                            }
                        } else if ((incompletos > 0 && confirm("⚠️ Hay productos incompletos. ¿Deseas continuar?"))) {                     
                            const clave = prompt("🔐 Ingresa la clave para continuar:");
                            if (clave !== "12345") {
                                alert("❌ Clave incorrecta. Operación cancelada.");
                                mostrarLoader(false);
                                return;
                            } else {
                                console.log("🔓 Clave correcta. Continuando con la transferencia...");
                                continuar = true;
                            }
                        } else {
                            alert("⚠️ Transferencia cancelada por el usuario.");
                            mostrarLoader(false);
                            return;
                        }
                        



                        if(continuar){
                               
                                console.log("📦 JSON :", stockTransfer);

                            const transferBody = {
                                Comments: stockTransfer.comments,
                                FromWarehouse: stockTransfer.fromWarehouse,
                                ToWarehouse: stockTransfer.toWarehouse,
                                PriceList: stockTransfer.priceList,
                                StockTransferLines: stockTransfer.stockTransferLines.map(line => ({
                                    ItemCode: line.itemCode,
                                    Quantity: line.quantity,
                                    WarehouseCode: line.warehouseCode,
                                    BaseEntry: line.baseEntry,
                                    BaseLine: line.baseLine,
                                    BaseType: line.baseType
                                }))
                            };
                console.log("📦 JSON generado:", transferBody);
                                            fetch('php/enviar_transferencia_stock.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(transferBody)
                })
                .then(async response => {
                    const text = await response.text();

                    console.log('📦 Respuesta de la API:', text);

                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        data = text;
                    }

                    console.log('Respuesta API StockTransfer:', data);

                    // Extrae mensaje, ya sea de JSON o texto plano
                    let msg = typeof data === 'string' 
                        ? data 
                        : (data && data.message 
                            ? data.message 
                            : JSON.stringify(data));

                    console.log('msg Mensaje de la API:', msg);

                    mostrarLoader(false);

                    if (msg.includes('Transferencia creada')) {
                        alert('✔️ ' + msg);

                        // Actualiza estado en base de datos
                        fetch("php/actualizar_estado_recep.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: new URLSearchParams({
                                id: "<?= $idcab ?>"
                            })
                        })
                        .then(() => {
                            // Redirige después de actualizar
                            setTimeout(() => {
                                window.location.href = "recPL.php";
                            }, 1500);
                        });

                    } else {
                        alert('🥲 ' + (msg || 'Error desconocido al crear la transferencia.'));
                    }
                })
                .catch(error => {
                    mostrarLoader(false);
                    console.error("❌ Error de red:", error);
                    alert("Error al conectar con el servidor.");
                });

                                
                                
                        }
        mostrarLoader(false);

    };

    function guardarCodigosDiferencias() {
        const codigos = Array.from(document.querySelectorAll("#tablaNoReconocidos tbody tr td:first-child"))
            .map(td => td.textContent.trim());

        if (codigos.length === 0) return;

    // 🔹 Imprimir en consola antes de guardar
    console.log("Códigos a guardar:", codigos);

        fetch('php/guardar_diferencias.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idcab: "<?= $idcab ?>", codigos: codigos })
        })
       .then(res => res.text()) // <- leer como texto crudo
        .then(resp => {
            console.log("Respuesta del servidor:", resp);
        })
        .catch(err => console.error("Error fetch:", err));
    }

function cargarCodigosDiferencias() {
    fetch('php/obtener_diferencias.php?idcab=<?= $idcab ?>')
    .then(res => res.json())
    .then(data => {
        const tbody = document.querySelector("#tablaNoReconocidos tbody");
        tbody.innerHTML = ""; // limpiar tabla
        data.forEach(item => {
            const fila = document.createElement("tr");
            const celdaCodigo = document.createElement("td");
            celdaCodigo.textContent = item.CodeBars;
            const celdaFecha = document.createElement("td");
            celdaFecha.textContent = item.datetime;
            fila.appendChild(celdaCodigo);
            fila.appendChild(celdaFecha);
            tbody.appendChild(fila);
        });
    });
}

// Ejecutar al cargar la página
document.addEventListener("DOMContentLoaded", cargarCodigosDiferencias);


    function mostrarAlertaNoReconocido(mensaje) {
        const alerta = document.getElementById("alertaCodigo");
        alerta.textContent = mensaje;
        alerta.style.display = "block";
        alerta.style.opacity = 1;

        setTimeout(() => {
            let opacidad = 1;
            const fade = setInterval(() => {
                if (opacidad <= 0.1) {
                    clearInterval(fade);
                    alerta.style.display = "none";
                }
                alerta.style.opacity = opacidad;
                opacidad -= 0.1;
            }, 100);
        }, 2000);
    }

    function agregarCodigoNoReconocido(codigo, motivo = "Código no encontrado") {
        const tabla = document.querySelector("#tablaNoReconocidos tbody");
        const fila = document.createElement("tr");

        const celdaCodigo = document.createElement("td");
        celdaCodigo.textContent = codigo;

        const celdaFecha = document.createElement("td");
        celdaFecha.textContent = new Date().toLocaleString();

        fila.appendChild(celdaCodigo);
        fila.appendChild(celdaFecha);
        tabla.appendChild(fila);

        mostrarAlertaNoReconocido(motivo);

        // Guardar en BD
        guardarCodigosDiferencias();
    }

    document.getElementById('btnDescargarCSV').addEventListener('click', function () {
        const tabla = document.querySelector('#tabla');
        if (!tabla) {
            alert("❌ No se encontró la tabla.");
            return;
        }

        let csv = [];
        const filas = tabla.querySelectorAll('tr');
        filas.forEach(fila => {
            const columnas = fila.querySelectorAll('th, td');
            const filaCSV = Array.from(columnas).map(col => `"${col.textContent.trim()}"`).join(',');
            csv.push(filaCSV);
        });

        const contenidoCSV = csv.join('\n');
        const blob = new Blob([contenidoCSV], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const enlace = document.createElement('a');
        enlace.href = url;
        enlace.download = 'exportado_tabla.csv';
        document.body.appendChild(enlace);
        enlace.click();
        document.body.removeChild(enlace);
    });

    function mostrarLoader(mostrar) {
        document.getElementById('loader').style.display = mostrar ? 'block' : 'none';
    }

    function obtenerResponsable() {
        return document.getElementById("responsable").value.trim();
    }

    document.getElementById('btnGuardar').addEventListener('click', function () {
        const responsable = obtenerResponsable();
        if (!responsable) {
            alert("❌ Debe ingresar el Responsable antes de guardar.");
            return;
        }

        mostrarLoader(true);

        const filas = document.querySelectorAll('#tabla tbody tr');
        let errorCritico = false;

        const datos = {
            responsable,
            idcab: "<?= $idcab ?>",   // id de la cabecera actual
            items: []
        };

        filas.forEach(fila => {
            const solicitado = parseInt(fila.cells[2].textContent);
            const escaneado = parseInt(fila.cells[3].textContent);
            if (escaneado > solicitado) {
                errorCritico = true;
            }

            datos.items.push({
                id: fila.getAttribute('data-id'),
                scan: escaneado
            });
        });

        if (errorCritico) {
            mostrarLoader(false);
            alert("❌ Error crítico: Hay productos con escaneado mayor al solicitado. Contacte con sistemas.");
            return;
        }

        // Guardar detalle y responsable en base de datos
        fetch('php/guardar_scans_recep.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("✅ Datos y Responsable guardados correctamente.");
            } else {
                alert("❌ Error: " + (data.message || "al guardar los datos."));
            }
            mostrarLoader(false);
        })
        .catch(error => {
            console.error("Error:", error);
            alert("❌ Error en la comunicación con el servidor.");
            mostrarLoader(false);
        });
    });

    // Pintar filas al cargar la página según el estado del escaneo
    window.addEventListener('DOMContentLoaded', () => {
        const filas = document.querySelectorAll('#tabla tbody tr');

        filas.forEach(fila => {
            const solicitado = parseInt(fila.cells[2].textContent);
            const escaneado = parseInt(fila.cells[3].textContent);

            fila.classList.remove('fila-completa', 'fila-parcial');
            
            if (escaneado === solicitado) {
                fila.classList.add('fila-completa'); // verde
            } else if (escaneado > 0 && escaneado < solicitado) {
                fila.classList.add('fila-parcial'); // gris
            } else if (escaneado > solicitado) {
                fila.style.backgroundColor = '#f8d7da'; // rojo claro
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const inputCodigo = document.getElementById("codigo");
        const inputCantidad = document.getElementById("txtu");
        const tabla = document.getElementById("tabla").getElementsByTagName("tbody")[0];
        const botonAgregar = document.getElementById("btnAgregar");

        function procesarCodigo() {
            const codigoIngresado = inputCodigo.value.trim();
            const cantidad = parseInt(inputCantidad.value.trim()) || 1;

            if (!codigoIngresado) {
                alert("Por favor ingresa un código de barras.");
                return;
            }

            let encontrado = false;

            Array.from(tabla.rows).forEach(fila => {
                const codigoTabla = fila.getAttribute("data-codigo");
                const solicitado = parseInt(fila.cells[2].textContent.trim());
                const escaneadoCell = fila.querySelector(".escaneados");
                let escaneado = parseInt(escaneadoCell.textContent.trim());

                if (codigoTabla === codigoIngresado) {
                    encontrado = true;

                    if (escaneado + cantidad > solicitado) {
                        alert("No puedes escanear más de lo solicitado.");
                        // 👇 también lo guardamos en la tabla de adicionales
                        agregarCodigoNoReconocido(codigoIngresado, "Código superó lo solicitado");
                    } else {

                        escaneado += cantidad;
                        escaneadoCell.textContent = escaneado;

                        tabla.insertBefore(fila, tabla.firstChild);
                        // Limpiar estilos anteriores
                        fila.classList.remove("fila-completa", "fila-parcial");
                        fila.style.backgroundColor = "";

                        // Aplicar color según condición
                        if (escaneado === solicitado) {
                            fila.classList.add("fila-completa"); // verde
                        } else if (escaneado > 0 && escaneado < solicitado) {
                            fila.classList.add("fila-parcial"); // gris
                        } else if (escaneado > solicitado) {
                            fila.style.backgroundColor = "#f8d7da"; // rojo claro
                        }
                    }
                }
            });

            // 👇 Aquí reemplazo el alert por la lógica de "no encontrado"
            if (!encontrado) {
                alert("❌ Codigo no pertenece a esta Solicitud.");
                agregarCodigoNoReconocido(codigoIngresado);
            }
            
            inputCantidad.value = "1"; // Resetear cantidad
            inputCodigo.value = "";
            inputCodigo.focus();
        }

        // Click en botón ➕
        botonAgregar.addEventListener("click", function (e) {
            e.preventDefault();
            procesarCodigo();
        });

        // Enter en input de código
        inputCodigo.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                procesarCodigo();
            }
        });
    
    });

    document.addEventListener("DOMContentLoaded", function () {
        const btn = document.getElementById("btnTransferencia");
        if (btn) {
            btn.addEventListener("click", async function () {
                // Ejecutar btnGuardar antes de crear transferencia
                await new Promise((resolve) => {
                    document.getElementById("btnGuardar").click();
                    // Esperar a que termine el guardado (simple delay de 1s, ajusta si tienes callback)
                    setTimeout(resolve, 1000);
                });
                crearTransferencia();
            });
        }
    });

</script>

<?php include_once "footer.php"; ?>
