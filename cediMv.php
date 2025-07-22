<?php
    include_once "header.php";
    if ($userAdmin <> 1) {
        echo ('<div class="alert alert-danger" role="alert">ACCESO DENEGADO</div>');
        include_once "footer.php";
        exit();
    }
    if (!isset($_SESSION["whsBodeg"]) || $_SESSION["whsBodeg"] == 0) {
        echo ('<div class="alert alert-danger" role="alert">
                    No tiene asignada una bodega para reubicación
                </div>');
        include_once "footer.php";
        exit();
    }
?>

<div class="content">


   
  <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header" style="display:flex;align-items:center;gap:8px;flex-wrap:nowrap;min-width:0;overflow:hidden;">
                            <strong>Reubicación</strong> artículos
                            <span id="warehouseCodeHeader"> </span>
                        </div>
                        <div class="card-body card-block">
                            <form action="#" method="post" class="form-horizontal" id="scanForm" onsubmit="return false;">
                                <div class="row form-group">
                                    <div class="col-10">
                                        <input list="origenList" type="text" placeholder="Ubicación Origen" class="form-control" id="origen" required autocomplete="off">
                                        <datalist id="origenList"></datalist>
                                    </div>
                                    <div class="col-2">
                                        <button>✔️</button>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3"><input type="text" placeholder="cantidad" class="form-control" id="cantidad" min="1" value="1" disabled></div>
                                    <div class="col-7"><input type="text" id="codigo" placeholder="Cod. Barras" class="form-control" disabled></div>
                                    <div class="col-2">
                                        <button onclick="agregarProducto()">➕</button>
                                    </div>
                                </div>
                            

                                    <div style="max-height: 325px; overflow-y: auto;">
                                        <table class="table mb-0" id="tablaProductos" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th style="width:45%">Código de Barras</th>
                                                    <th style="width:35%">ItemCode</th>
                                                    <th style="width:20%">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div><br>


                                <div class="row form-group">
                                    <div class="col-12">
                                        <input list="destinoList" type="text" placeholder="Ubicación Destino" class="form-control" id="destino" required disabled autocomplete="off">
                                        <datalist id="destinoList"></datalist>
                                    </div>
                                </div>
                                <div class="form-group" style="display:flex; gap:10px; justify-content:flex-end;">
                                    <button type="submit" onclick="aceptar()" class="btn btn-outline-success btn-sm">Enviar</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnReset">Reset</button>
                                </div>
                            </form>
                        </div>
                        <!--<div class="card-footer"></div>-->
                    </div>
                </div>





</div>
<script>

    const productos = [];
    let productosOrigen = []; // Lista de productos válidos para la ubicación origen
    let productosOrigenAbsEntry = null; // Para saber si ya se consultó para este AbsEntry

    // Habilita/deshabilita campos según el flujo
    function setScanFields(enabled) {
        document.getElementById('cantidad').disabled = !enabled;
        document.getElementById('codigo').disabled = !enabled;
        document.getElementById('destino').disabled = !enabled;
        document.querySelector('.btn.btn-outline-success.btn-sm[type="submit"]').disabled = !enabled;
    }

    function renderTabla() {
        const tabla = document.getElementById('tablaProductos').getElementsByTagName('tbody')[0];
        tabla.innerHTML = '';
        productos.forEach((prod, idx) => {
            const fila = tabla.insertRow();
            // Buscar el producto en productosOrigen para mostrar el ItemCode
            let itemCode = '';
            const prodOrigen = productosOrigen.find(p => p.CodeBars === prod.codigo || p.ItemCode === prod.codigo);
            if (prodOrigen) {
                itemCode = prodOrigen.ItemCode;
            }
            fila.insertCell(0).textContent = prod.codigo;
            fila.insertCell(1).textContent = itemCode;
            const cellCantidad = fila.insertCell(2);
            cellCantidad.textContent = prod.cantidad;
            // Permitir edición con doble clic
            cellCantidad.ondblclick = function() {
                editarCantidad(idx, cellCantidad);
            };
        });
    }

    function editarCantidad(idx, cell) {
        mostrarModalAccion(idx, cell);
    }

    // Modal reutilizable para contraseña y acción
    function mostrarModalAccion(idx, cell) {
        let modal = document.getElementById('modalAccionLinea');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'modalAccionLinea';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.background = 'rgba(0,0,0,0.3)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = 10001;
            modal.innerHTML = `
                <div style="background:#fff;padding:30px 30px 20px 30px;border-radius:10px;min-width:320px;box-shadow:0 0 20px #0003;display:flex;flex-direction:column;align-items:center;">
                    <div style='font-weight:bold;font-size:1.1em;margin-bottom:10px;'>Acción sobre la línea</div>
                    <input type="password" id="modalPass" class="form-control" placeholder="Contraseña" style="margin-bottom:10px;width:200px;">
                    <div id="modalError" style="color:#c00;font-size:0.95em;display:none;margin-bottom:10px;"></div>
                    <div style="display:flex;gap:10px;">
                        <button id="btnEditarLinea" class="btn btn-primary btn-sm">Editar</button>
                        <button id="btnBorrarLinea" class="btn btn-danger btn-sm">Borrar</button>
                        <button id="btnCancelarLinea" class="btn btn-secondary btn-sm">Cancelar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        modal.style.display = 'flex';
        document.getElementById('modalPass').value = '';
        document.getElementById('modalError').style.display = 'none';

        // Botones
        const btnEditar = document.getElementById('btnEditarLinea');
        const btnBorrar = document.getElementById('btnBorrarLinea');
        const btnCancelar = document.getElementById('btnCancelarLinea');
        // Limpiar listeners previos
        btnEditar.onclick = btnBorrar.onclick = btnCancelar.onclick = null;

        // Contraseña
        const PASSWORD_CORRECTA = '12345';
        function validarPass() {
            const pass = document.getElementById('modalPass').value;
            if (pass !== PASSWORD_CORRECTA) {
                document.getElementById('modalError').textContent = 'Contraseña incorrecta.';
                document.getElementById('modalError').style.display = 'block';
                return false;
            }
            document.getElementById('modalError').style.display = 'none';
            return true;
        }

        btnEditar.onclick = function() {
            if (!validarPass()) return;
            modal.style.display = 'none';
            // Editar cantidad
            const input = document.createElement('input');
            input.type = 'number';
            input.min = '0';
            input.value = productos[idx].cantidad;
            input.style.width = '60px';
            input.onblur = function() {
                guardarCantidad(idx, input, cell);
            };
            input.onkeydown = function(e) {
                if (e.key === 'Enter') {
                    input.blur();
                }
            };
            cell.textContent = '';
            cell.appendChild(input);
            input.focus();
            input.select();
        };
        btnBorrar.onclick = function() {
            if (!validarPass()) return;
            if (confirm('¿Está seguro de que desea eliminar esta línea?')) {
                productos.splice(idx, 1);
                renderTabla();
                modal.style.display = 'none';
            }
        };
        btnCancelar.onclick = function() {
            modal.style.display = 'none';
        };
        // Enter en input ejecuta editar
        document.getElementById('modalPass').onkeydown = function(e) {
            if (e.key === 'Enter') btnEditar.click();
        };
        setTimeout(() => { document.getElementById('modalPass').focus(); }, 100);
    }

    function guardarCantidad(idx, input, cell) {
        let val = parseInt(input.value);
        // Buscar el OnHandQty del producto correspondiente
        let maxCantidad = 0;
        const prod = productos[idx];
        const prodOrigen = productosOrigen.find(p => p.CodeBars === prod.codigo || p.ItemCode === prod.codigo);
        if (prodOrigen) {
            maxCantidad = parseInt(prodOrigen.OnHandQty);
        }
        if (isNaN(val) || val <= 0 || (maxCantidad > 0 && val > maxCantidad)) {
            alert('La cantidad debe ser mayor a 0 y menor o igual al stock disponible (' + maxCantidad + ').');
            // Restaurar el valor anterior y salir del modo edición
            cell.textContent = productos[idx].cantidad;
            cell.ondblclick = function() {
                editarCantidad(idx, cell);
            };
            return;
        }
        productos[idx].cantidad = val;
        cell.textContent = val;
        cell.ondblclick = function() {
            editarCantidad(idx, cell);
        };
    }

    // Al cargar, solo habilitado origen
    // Cargar ubicaciones para el datalist de origen y destino
    let ubicacionesData = [];
    function cargarUbicaciones() {
        fetch('php/ubicaciones_ajax.php')
            .then(response => response.json())
            .then(data => {
                ubicacionesData = data;
                const datalistOrigen = document.getElementById('origenList');
                datalistOrigen.innerHTML = '';
                data.forEach(u => {
                    const option = document.createElement('option');
                    option.value = u.BinCode;
                    datalistOrigen.appendChild(option);
                });
                cargarDestinoList();
            });
    }

    // Mostrar/ocultar loader
    function mostrarLoader(mostrar) {
        let loader = document.getElementById('loader-ubicacion');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'loader-ubicacion';
            loader.style.position = 'fixed';
            loader.style.top = 0;
            loader.style.left = 0;
            loader.style.width = '100vw';
            loader.style.height = '100vh';
            loader.style.background = 'rgba(255,255,255,0.7)';
            loader.style.zIndex = 9999;
            loader.style.display = 'flex';
            loader.style.alignItems = 'center';
            loader.style.justifyContent = 'center';
            loader.innerHTML = '<div id="loader-ubicacion-msg" style="background:#fff;padding:30px 50px;border-radius:10px;box-shadow:0 0 10px #aaa;font-size:1.2em;display:flex;align-items:center;"><span class="spinner-border text-primary" style="margin-right:15px;width:2rem;height:2rem;"></span> <span id="loader-ubicacion-text">Consultando productos de la ubicación origen...</span></div>';
            document.body.appendChild(loader);
        }
        // Permitir mensaje custom
        let text = 'Consultando productos de la ubicación origen...';
        if (typeof mostrar === 'string') text = mostrar;
        if (typeof mostrar === 'boolean' && mostrar) text = 'Consultando productos de la ubicación origen...';
        if (loader.querySelector('#loader-ubicacion-text')) loader.querySelector('#loader-ubicacion-text').textContent = text;
        loader.style.display = mostrar ? 'flex' : 'none';
    }

    // Cargar productos de la ubicación origen y guardarlos en productosOrigen
    function cargarProductosOrigen(absEntry) {
        mostrarLoader(true);
        return fetch('productos_ubicacion_ajax.php?absEntry=' + encodeURIComponent(absEntry))
            .then(r => r.json())
            .then(data => {
                productosOrigen = Array.isArray(data) ? data : [];
                // Opcional: mostrar en consola para debug
                console.log('Productos válidos para escaneo:', productosOrigen);
            })
            .catch(() => {
                productosOrigen = [];
            })
            .finally(() => {
                mostrarLoader(false);
            });
    }

    function cargarDestinoList() {
        const datalistDestino = document.getElementById('destinoList');
        datalistDestino.innerHTML = '';
        const origenVal = document.getElementById('origen').value.trim();
        ubicacionesData.forEach(u => {
            if (u.BinCode !== origenVal) {
                const option = document.createElement('option');
                option.value = u.BinCode;
                datalistDestino.appendChild(option);
            }
        });
    }

    let escaneoEnCurso = false;
    window.onload = function() {
        cargarUbicaciones();
        setScanFields(false);
        document.getElementById('origen').disabled = false;
        document.getElementById('destino').disabled = true;
        document.querySelector('.btn.btn-outline-success.btn-sm[type="submit"]').disabled = true;
        document.getElementById('origen').focus();
        renderTabla();
        // Mostrar WarehouseCode en el header
        fetch('get_warehouse_code.php?id=<?php echo $whsBodega; ?>')
            .then(resp => resp.json())
            .then(data => {
                if (data && data.cod_almacen) {
                    document.getElementById('warehouseCodeHeader').textContent = '(' + data.cod_almacen + ')';
                }
            });
        // Referencia al botón de visto (✔️) al lado de origen
        const btnVisto = document.querySelector('.row.form-group .col-2 button');
        // Validar destino al salir del campo o cambiar
        document.getElementById('destino').addEventListener('blur', function() {
            if (this.value && !validarDestino()) {
                alert('Debe elegir una ubicación de destino válida de la lista.');
                this.value = '';
                this.focus();
            }
        });
        document.getElementById('destino').addEventListener('change', function() {
            if (this.value && !validarDestino()) {
                alert('Debe elegir una ubicación de destino válida de la lista.');
                this.value = '';
                this.focus();
            }
        });
        function habilitarEscaneo() {
            if (escaneoEnCurso) return;
            const origenVal = document.getElementById('origen').value.trim();
            if (origenVal !== '') {
                // Validar que la ubicación origen exista en la lista
                const origenObj = ubicacionesData.find(u => u.BinCode === origenVal);
                if (!origenObj) {
                    alert('Debe elegir una ubicación de origen válida de la lista.');
                    document.getElementById('origen').value = '';
                    document.getElementById('origen').focus();
                    return;
                }
                escaneoEnCurso = true;
                // Si ya se consultó productosOrigen para este AbsEntry, usarlo
                if (productosOrigen && productosOrigen.length > 0 && productosOrigenAbsEntry === origenObj.AbsEntry) {
                    // Ya consultado y no vacío
                    setScanFields(true);
                    document.getElementById('codigo').focus();
                    document.getElementById('origen').disabled = true;
                    btnVisto.disabled = true;
                    document.getElementById('destino').disabled = false;
                    document.querySelector('.btn.btn-outline-success.btn-sm[type="submit"]').disabled = false;
                    cargarDestinoList();
                    escaneoEnCurso = false;
                    return;
                }
                cargarProductosOrigen(origenObj.AbsEntry).then(() => {
                    productosOrigenAbsEntry = origenObj.AbsEntry;
                    if (!productosOrigen || productosOrigen.length === 0) {
                        // Ubicación origen vacía
                        alert('La ubicación de origen está vacía. Seleccione otra ubicación.');
                        // Resetear el formulario
                        document.querySelector('.btn.btn-warning.btn-sm').click();
                        escaneoEnCurso = false;
                        return;
                    }
                    setScanFields(true);
                    document.getElementById('codigo').focus();
                    document.getElementById('origen').disabled = true;
                    btnVisto.disabled = true;
                    document.getElementById('destino').disabled = false;
                    document.querySelector('.btn.btn-outline-success.btn-sm[type="submit"]').disabled = false;
                    cargarDestinoList();
                    escaneoEnCurso = false;
                });
            }
        }
        document.getElementById('origen').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                habilitarEscaneo();
            }
        });
        // Botón ✔️ al lado de origen
        btnVisto.addEventListener('click', function(e) {
            e.preventDefault();
            habilitarEscaneo();
        });
        // Enter en código de barras actúa como el botón ▶️
        document.getElementById('codigo').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                agregarProducto();
            }
        });
        document.getElementById('destino').addEventListener('focus', function() {
            // No deshabilitar campos al enfocar destino
        });
        document.getElementById('destino').addEventListener('change', function() {
            // No deshabilitar campos al cambiar destino
        });
        // Reset button
        document.getElementById('btnReset').addEventListener('click', function(e) {
            e.preventDefault();
            // Limpiar todos los campos y productos
            document.getElementById('origen').value = '';
            document.getElementById('origen').disabled = false;
            document.getElementById('cantidad').value = '1';
            document.getElementById('cantidad').disabled = true;
            document.getElementById('codigo').value = '';
            document.getElementById('codigo').disabled = true;
            document.getElementById('destino').value = '';
            document.getElementById('destino').disabled = true;
            document.querySelector('.btn.btn-outline-success.btn-sm[type="submit"]').disabled = true;
            productos.length = 0;
            renderTabla();
            cargarUbicaciones();
            document.getElementById('origen').focus();
        });
    };

    // Validar que el destino exista en la lista
    function validarDestino() {
        const destinoVal = document.getElementById('destino').value.trim();
        if (!destinoVal) return false;
        return ubicacionesData.some(u => u.BinCode === destinoVal);
    }

    function agregarProducto() {
        if (document.getElementById('cantidad').disabled || document.getElementById('codigo').disabled) return;
        const codigo = document.getElementById('codigo').value.trim();
        let cantidad = parseInt(document.getElementById('cantidad').value);

        // Validar que el código esté en la lista de productosOrigen
        const prodValido = productosOrigen.find(p => p.CodeBars === codigo);
        if (!prodValido) {
            alert('El código de barras ingresado no está en la ubicación origen.');
            document.getElementById('codigo').focus();
            return;
        }

        // Calcular la cantidad ya agregada de este código
        const cantidadYaAgregada = productos
            .filter(p => p.codigo === codigo)
            .reduce((sum, p) => sum + p.cantidad, 0);

        // Validar cantidad máxima
        const maxCantidad = parseInt(prodValido.OnHandQty);
        if (isNaN(cantidad) || cantidad <= 0) {
            alert('Ingrese una cantidad válida.');
            document.getElementById('cantidad').focus();
            return;
        }
        if (cantidadYaAgregada + cantidad > maxCantidad) {
            alert('La cantidad total para este código no puede superar el stock disponible (' + maxCantidad + ').');
            document.getElementById('cantidad').focus();
            return;
        }

        // Agregar o actualizar producto
        const idx = productos.findIndex(p => p.codigo === codigo);
        if (idx !== -1) {
            productos[idx].cantidad += cantidad;
            // Mover a primer lugar
            const prod = productos.splice(idx, 1)[0];
            productos.unshift(prod);
        } else {
            productos.unshift({ codigo, cantidad });
        }
        renderTabla();
        document.getElementById('codigo').value = '';
        document.getElementById('cantidad').value = '1';
        document.getElementById('codigo').focus();
    }

    async function aceptar() {
        const origen = document.getElementById('origen').value.trim();
        const destino = document.getElementById('destino').value.trim();

        if (!origen) {
            alert("Ingrese la ubicación de origen.");
            document.getElementById('origen').focus();
            return;
        }
        if (!destino) {
            alert("Ingrese la ubicación de destino.");
            document.getElementById('destino').focus();
            return;
        }
        if (!validarDestino()) {
            alert('Debe elegir una ubicación de destino válida de la lista.');
            document.getElementById('destino').focus();
            return;
        }
        if (productos.length === 0) {
            alert("Agregue al menos un producto.");
            setScanFields(true);
            document.getElementById('codigo').focus();
            return;
        }

        // Obtener el WarehouseCode (ej: RL-SJ) desde el backend usando el id PHP $whsBodega
        let warehouseCode = '';
        try {
            const resp = await fetch('get_warehouse_code.php?id=<?php echo $whsBodega; ?>');
            if (resp.ok) {
                const data = await resp.json();
                warehouseCode = data.cod_almacen || '';
            }
        } catch (e) {}
        if (!warehouseCode) {
            alert('No se pudo obtener el WarehouseCode.');
            return;
        }

        // Buscar AbsEntry de origen y destino
        const origenObj = ubicacionesData.find(u => u.BinCode === origen);
        const destinoObj = ubicacionesData.find(u => u.BinCode === destino);
        if (!origenObj || !destinoObj) {
            alert('No se pudo obtener la ubicación origen o destino.');
            return;
        }
        const absEntryOrigen = origenObj.AbsEntry;
        const absEntryDestino = destinoObj.AbsEntry;

        // Construir StockTransferLines
        const StockTransferLines = productos.map(prod => {
            // Buscar el ItemCode real
            let itemCode = '';
            const prodOrigen = productosOrigen.find(p => p.CodeBars === prod.codigo || p.ItemCode === prod.codigo);
            if (prodOrigen) {
                itemCode = prodOrigen.ItemCode;
            }
            return {
                ItemCode: itemCode,
                Quantity: parseFloat(prod.cantidad),
                WarehouseCode: warehouseCode,
                StockTransferLinesBinAllocations: [
                    {
                        BinAbsEntry: absEntryOrigen,
                        Quantity: parseFloat(prod.cantidad),
                        BinActionType: 2 // Salida
                    },
                    {
                        BinAbsEntry: absEntryDestino,
                        Quantity: parseFloat(prod.cantidad),
                        BinActionType: 1 // Entrada
                    }
                ]
            };
        });

        const json = {
            Comments: 'sc22',
            FromWarehouse: warehouseCode,
            ToWarehouse: warehouseCode,
            StockTransferLines
        };

        // Mostrar el JSON generado en consola
        console.log('JSON generado para envío:', json);

        mostrarLoader('Espere mientras se procesa');
        try {
            const resp = await fetch('php/enviar_transferencia_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(json)
            });
            const text = await resp.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = text;
            }
            console.log('Respuesta API StockTransfer:', data);
            let msg = typeof data === 'string' ? data : (data && data.message ? data.message : JSON.stringify(data));
            if (msg && msg.includes('Transferencia creada exitosamente')) {
                // Oculta alerta roja si está visible
                let alertaRoja = document.getElementById('alerta-roja-api');
                if (alertaRoja) alertaRoja.style.display = 'none';
                mostrarAlertaVerde('Transferencia creada exitosamente.');
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 1500);
            } else {
                mostrarAlertaRoja(msg || 'Error desconocido al crear la transferencia.');
            }
        } catch (e) {
            console.error('Error al enviar a la API:', e);
            mostrarAlertaRoja('Error al enviar a la API. Ver consola para detalles.');
        } finally {
            mostrarLoader(false);
        }

    // Alerta roja flotante para errores
    function mostrarAlertaRoja(mensaje) {
        let alerta = document.getElementById('alerta-roja-api');
        if (!alerta) {
            alerta = document.createElement('div');
            alerta.id = 'alerta-roja-api';
            alerta.style.position = 'fixed';
            alerta.style.top = '30px';
            alerta.style.left = '50%';
            alerta.style.transform = 'translateX(-50%)';
            alerta.style.background = '#dc3545';
            alerta.style.color = '#fff';
            alerta.style.padding = '16px 32px';
            alerta.style.borderRadius = '8px';
            alerta.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
            alerta.style.fontSize = '1.1em';
            alerta.style.zIndex = 10000;
            alerta.style.display = 'none';
            alerta.style.fontWeight = 'bold';
            alerta.innerHTML = '';
            document.body.appendChild(alerta);
        }
        alerta.innerHTML = '<span style="margin-right:10px;">&#9888;</span>' + mensaje;
        alerta.style.display = 'block';
        setTimeout(() => {
            alerta.style.display = 'none';
        }, 5000);
    }

    // Alerta verde flotante para éxito
    function mostrarAlertaVerde(mensaje) {
        let alerta = document.getElementById('alerta-verde-api');
        if (!alerta) {
            alerta = document.createElement('div');
            alerta.id = 'alerta-verde-api';
            alerta.style.position = 'fixed';
            alerta.style.top = '30px';
            alerta.style.left = '50%';
            alerta.style.transform = 'translateX(-50%)';
            alerta.style.background = '#28a745';
            alerta.style.color = '#fff';
            alerta.style.padding = '16px 32px';
            alerta.style.borderRadius = '8px';
            alerta.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
            alerta.style.fontSize = '1.1em';
            alerta.style.zIndex = 10000;
            alerta.style.display = 'none';
            alerta.style.fontWeight = 'bold';
            alerta.innerHTML = '';
            document.body.appendChild(alerta);
        }
        alerta.innerHTML = '<span style="margin-right:10px;">&#10004;</span>' + mensaje;
        alerta.style.display = 'block';
        setTimeout(() => {
            alerta.style.display = 'none';
        }, 2000);
    }
    }
</script>

<?php include_once "footer.php"; ?>



