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
                        <div class="card-header">
                           <strong>Reubicación</strong> artículos<small><code><?php echo $whsBodega; ?></code></small>
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
                                    <div class="col-2"><input type="text" placeholder="cantidad" class="form-control" id="cantidad" min="1" value="1" disabled></div>
                                    <div class="col-8"><input type="text" id="codigo" placeholder="Cod. Barras" class="form-control" disabled></div>
                                    <div class="col-2">
                                        <button onclick="agregarProducto()">▶️</button>
                                    </div>
                                </div>
                            

                                    <div style="max-height: 325px; overflow-y: auto;">
                                        <table class="table mb-0" id="tablaProductos" style="table-layout: fixed;">
                                            <thead>
                                                <tr>
                                                    <th style="width:70%">Código de Barras</th>
                                                    <th style="width:30%">Cantidad</th>
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
                            </form>
                        </div>
                        <div class="card-footer">
                            <button type="submit" onclick="aceptar()" class="btn btn-primary btn-sm">Enviar</button>

                            <button class="btn btn-warning btn-sm">Reset</button>
                            <button class="btn btn-danger btn-sm">Salir</button>

                        </div>
                    </div>
                </div>





</div>
<script>
    const productos = [];

    // Habilita/deshabilita campos según el flujo
    function setScanFields(enabled) {
        document.getElementById('cantidad').disabled = !enabled;
        document.getElementById('codigo').disabled = !enabled;
        document.getElementById('destino').disabled = !enabled;
        document.querySelector('.btn.btn-primary.btn-sm').disabled = !enabled;
    }

    function renderTabla() {
        const tabla = document.getElementById('tablaProductos').getElementsByTagName('tbody')[0];
        tabla.innerHTML = '';
        productos.forEach((prod, idx) => {
            const fila = tabla.insertRow();
            fila.insertCell(0).textContent = prod.codigo;
            const cellCantidad = fila.insertCell(1);
            cellCantidad.textContent = prod.cantidad;
            // Permitir edición con doble clic
            cellCantidad.ondblclick = function() {
                editarCantidad(idx, cellCantidad);
            };
        });
    }

    function editarCantidad(idx, cell) {
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
    }

    function guardarCantidad(idx, input, cell) {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 0) val = 0;
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

    window.onload = function() {
        cargarUbicaciones();
        setScanFields(false);
        document.getElementById('origen').disabled = false;
        document.getElementById('destino').disabled = true;
        document.querySelector('.btn.btn-primary.btn-sm').disabled = true;
        document.getElementById('origen').focus();
        renderTabla();
        function habilitarEscaneo() {
            if (document.getElementById('origen').value.trim() !== '') {
                setScanFields(true);
                document.getElementById('codigo').focus();
                document.getElementById('origen').disabled = true;
                document.getElementById('destino').disabled = false;
                document.querySelector('.btn.btn-primary.btn-sm').disabled = false;
                cargarDestinoList();
            }
        }
        document.getElementById('origen').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                habilitarEscaneo();
            }
        });
        // Botón ✔️ al lado de origen
        document.querySelector('.row.form-group .col-2 button').addEventListener('click', function(e) {
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
        document.querySelector('.btn.btn-warning.btn-sm').addEventListener('click', function(e) {
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
            document.querySelector('.btn.btn-primary.btn-sm').disabled = true;
            productos.length = 0;
            renderTabla();
            cargarUbicaciones();
            document.getElementById('origen').focus();
        });
    };

    function agregarProducto() {
        if (document.getElementById('cantidad').disabled || document.getElementById('codigo').disabled) return;
        const codigo = document.getElementById('codigo').value.trim();
        const cantidad = parseInt(document.getElementById('cantidad').value);

        if (codigo && cantidad > 0) {
            // Buscar si el producto ya existe
            const idx = productos.findIndex(p => p.codigo === codigo);
            if (idx !== -1) {
                // Sumar cantidad si ya existe
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
        } else {
            alert("Ingrese un código y una cantidad válida.");
        }
    }

    function aceptar() {
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
        if (productos.length === 0) {
            alert("Agregue al menos un producto.");
            setScanFields(true);
            document.getElementById('codigo').focus();
            return;
        }

        // Aquí podrías enviar los datos al servidor con fetch/AJAX
        console.log("Origen:", origen);
        console.log("Productos:", productos);
        console.log("Destino:", destino);
        alert("Datos enviados correctamente.");
    }
</script>

<?php include_once "footer.php"; ?>
