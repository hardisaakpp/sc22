<?php
    include_once "header.php";

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
    $s1 = $db->query("
            SELECT [ID]
                ,[ItemCode]
                ,[Quantity] as stock
                ,[Scan]
                    , 0 as [DocNum_Sot]
                    ,0 as [DocEntry_Sot]
                ,[Dscription]  as descripcion
                ,[CodeBars] as codigoBarras
                ,[fk_idgroup]
                ,0 AS LineNum
            FROM [dbo].[ced_grouprecolCE]
            where fk_idgroup=".$idcab."  " );
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);

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


    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;gap:8px;flex-wrap:nowrap;min-width:0;overflow:hidden;">
                <strong>Recoleccion </strong> Grupo <?php echo $idcab ?>
                <span id="warehouseCodeHeader"> </span>
            </div>
           
            <div class="card-body card-block">
                <form id="form-codigo">
                        <div class="input-group mb-3 flex-nowrap">
                            <div class="col-2"><input type="text" placeholder="cantidad" id="txtu" class="form-control" id="cantidad" min="1" value="1"></div>
                            <div class="col-7"><input type="text" id="codigo" placeholder="Cod. Barras" class="form-control" autofocus></div>
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
                            <tr data-codigo="<?= $item->codigoBarras ?>" data-id="<?= $item->ID ?>" data-itemcode="<?= $item->ItemCode ?>" data-docentry="<?= $item->DocEntry_Sot ?>" data-linenum="<?= $item->LineNum ?>" data-id="<?= $item->ID ?>">
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
                    
                    <button id="btnDescargarCSV" class="btn btn-outline-info btn-sm">📥 Exportar</button>
                </div>
            </div>
        </div>
    </div>

      
</div>

<script>
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


    document.getElementById('btnGuardar').addEventListener('click', function () {
        mostrarLoader(true); // Mostrar loader

        const filas = document.querySelectorAll('#tabla tbody tr');
        let errorCritico = false;
        const datos = [];

        filas.forEach(fila => {
            const solicitado = parseInt(fila.cells[2].textContent);
            const escaneado = parseInt(fila.cells[3].textContent);
            if (escaneado > solicitado) {
                errorCritico = true;
            }

            datos.push({
                id: fila.getAttribute('data-id'),
                scan: escaneado
            });
        });

        if (errorCritico) {
            mostrarLoader(false);
            alert("❌ Error crítico: Hay productos con escaneado mayor al solicitado. Contacte con sistemas.");
            return;
        }

        fetch('php/guardar_scans_recolCE.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("✅ Datos guardados correctamente.");
            } else {
                alert("❌ Error al guardar los datos.");
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

        if (!encontrado) {
            alert("❌ Código no encontrado en la tabla.");
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






</script>

<?php include_once "footer.php"; ?>
