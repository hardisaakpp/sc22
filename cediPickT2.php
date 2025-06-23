<?php
include_once "header.php";

if (!isset($_GET["idcab"])) {
    exit();
}
$idcab = $_GET["idcab"];

$s11 = $db->query("SELECT g.[id]
      ,g.[fk_idgroup]
      ,g.[estado]
      ,g.[fk_docnumsotcab]
      ,g.[activo]
	  ,c.ToWhsCode
	  ,c.DocDate , c.Filler
  FROM [dbo].[ced_groupsot] g 
	join SotCab_MT c on g.fk_docnumsotcab=c.DocNum
            WHERE g.id = ".$idcab." ");
$TEMPa1 = $s11->fetchObject();
       $ToWhsCode = $TEMPa1->ToWhsCode;
      // $nomealmacenCica = $TEMPa1->nombre;

$s1 = $db->query("SELECT [ID]
      ,[ItemCode]
      ,[Quantity] as stock
      ,[Scan]
      ,[DocNum_Sot]
      ,[DocEntry_Sot]
      ,[Dscription] as descripcion
      ,[CodeBars] as codigoBarras
      ,[fk_idgroup]
      ,LineNum
  FROM [dbo].[ced_groupsotdet]
            WHERE DocNum_Sot = ".$TEMPa1->fk_docnumsotcab." and fk_idgroup = ".$TEMPa1->fk_idgroup." " );
$scans = $s1->fetchAll(PDO::FETCH_OBJ);
?>

<?php
function sendStockTransfer($jsonPayload) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://192.168.2.12:8086/api/StockTransfer',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jsonPayload,
        CURLOPT_HTTPHEADER => array(
            'User-Agent: Apidog/1.0.0 (https://apidog.com)',
            'Content-Type: application/json',
            'Accept: */*',
            'Host: 192.168.2.12:8086',
            'Connection: keep-alive'
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error = curl_error($curl);
        curl_close($curl);
        return json_encode(["error" => $error]);
    }

    curl_close($curl);
    return $response;
}
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">SOLICITUD N°<?php echo $idcab." a ".$ToWhsCode ?> </strong>  
            </div>
            <form id="form-codigo">
                <div class="input-group mb-3 flex-nowrap">
                    <input type="number" class="form-control" id="txtu" placeholder="U" value="1" min="1">   
                    <input type="text" class="form-control" id="codigo" placeholder="Escanea o escribe el código" autofocus>
                    <div class="input-group-append dropdown">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">➕</button>
                            <button id="btnGuardar" class="btn btn-success">💾</button>
                        </div>
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

            <div class="mt-3">
                <button id="btnTransferencia" class="btn btn-warning">Crear Transferencia</button>
                
                <button id="btnDescargar" class="btn btn-info mt-2">⬇️ Descargar Tabla</button>

            </div>
        </div>
    </div>
</div>
<div id="mensajeSimple" style="display:none; position:fixed; top:10px; left:50%; transform:translateX(-50%); background:#d4edda; color:#155724; padding:10px 20px; border-radius:5px; z-index:1000;">
</div>

<script>
    document.getElementById('form-codigo').addEventListener('submit', function(e) {
        e.preventDefault();
        const codigoIngresado = document.getElementById('codigo').value.trim();
        const unidades = parseInt(document.getElementById('txtu').value) || 1;
        if (!codigoIngresado || unidades < 1) return;

        const tabla = document.querySelector('#tabla tbody');
        let fila = document.querySelector(`tr[data-codigo="${codigoIngresado}"]`);

        if (fila) {
            const escaneadosCelda = fila.querySelector('.escaneados');
            const cantidadActual = parseInt(escaneadosCelda.textContent) || 0;
            const stockCelda = fila.children[2];
            const stock = parseInt(stockCelda.textContent) || 0;

            if (cantidadActual + unidades > stock) {
                mostrarMensaje("Este producto "+document.getElementById('codigo').value+" ya ha sido completado. No se puede escanear más.");
                document.getElementById('txtu').value = 1;    
                document.getElementById('codigo').value = '';
                document.getElementById('codigo').focus();
                return;
            }

            const nuevaCantidad = cantidadActual + unidades;
            escaneadosCelda.textContent = nuevaCantidad;
            fila.classList.remove('fila-completa', 'fila-parcial');
            if (nuevaCantidad === stock) {
                fila.classList.add('fila-completa');
            } else if (nuevaCantidad > 0 && nuevaCantidad < stock) {
                fila.classList.add('fila-parcial');
            }
            tabla.prepend(fila);
        } else {
            alert("El código "+document.getElementById('codigo').value+" no existe en la lista. Verifica e intenta nuevamente.");
        }
        document.getElementById('txtu').value = 1;
        document.getElementById('codigo').value = '';
        document.getElementById('codigo').focus();
    });
function mostrarLoader(mostrar) {
  document.getElementById('loader').style.display = mostrar ? 'block' : 'none';
}
document.getElementById('btnDescargar').addEventListener('click', function () {
    const filas = document.querySelectorAll('#tabla tbody tr');
    let csv = "Código,Artículo,Solicitado,Escaneado\n";

    filas.forEach(fila => {
        const columnas = fila.querySelectorAll('td');
        const filaCSV = Array.from(columnas).map(col => `"${col.textContent.trim()}"`).join(",");
        csv += filaCSV + "\n";
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "tabla_scans.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});

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

    fetch('php/guardar_scans.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        mostrarLoader(false);
        if (data.status === "success") {
            alert("✅ Datos guardados correctamente.");
        } else {
            alert("❌ Error al guardar los datos.");
        }
    })
    .catch(error => {
        mostrarLoader(false);
        console.error("Error:", error);
        alert("❌ Error en la comunicación con el servidor.");
    });
});


    
document.getElementById('btnTransferencia').addEventListener('click', async function () {

mostrarLoader(true); // Mostrar loader

//guardar

     // Ejecutar el mismo código de guardar
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

     try {
     const response = await fetch('php/guardar_scans.php', {
     method: 'POST',
     headers: { 'Content-Type': 'application/json' },
     body: JSON.stringify(datos)
     });
     const data = await response.json();
     if (data.status !== "success") {
     mostrarLoader(false);
     alert("❌ Error al guardar los datos.");
     return;
     }
     } catch (error) {
     mostrarLoader(false);
     alert("❌ Error en la comunicación con el servidor.");
     return;
     }


//transferencia



       // const filas = document.querySelectorAll('#tabla tbody tr');
        let incompletos = 0;
        let totalEscaneado = 0;
       // let errorCritico = false;

        filas.forEach(fila => {
            const solicitado = parseInt(fila.cells[2].textContent);
            const escaneado = parseInt(fila.cells[3].textContent);
            if (escaneado > solicitado) {
                errorCritico = true;
            }
            if (escaneado < solicitado) {
                incompletos++;
            }
            if (escaneado > 0) {
                totalEscaneado++;
            }
        });

        if (errorCritico) {
            alert("❌ Error crítico: Hay productos con escaneado mayor al solicitado. Contacte con sistemas.");
                 mostrarLoader(false);
            return;
        }

// Generar JSON de transferencia
        const stockTransfer = {
            cardCode: "",
            comments: "sc22",
            fromWarehouse: "<?= $TEMPa1->Filler ?>",
            toWarehouse: "<?= $TEMPa1->ToWhsCode ?>",
            priceList: -2,
            stockTransferLines: []
        };

        filas.forEach(fila => {
            const escaneado = parseInt(fila.cells[3].textContent);
            if (escaneado > 0) {
                const item = {
                    itemCode: fila.getAttribute('data-itemcode'),
                    quantity: escaneado,
                    warehouseCode: "<?= $TEMPa1->ToWhsCode ?>",
                    baseEntry: parseInt(fila.getAttribute('data-docentry')),
                    baseLine: parseInt(fila.getAttribute('data-linenum')),
                    baseType: 1250000001
                };
                stockTransfer.stockTransferLines.push(item);
            }
        });



        if (totalEscaneado === 0) {
            alert("⚠️ No se ha escaneado ningún producto. No se puede crear la transferencia.");
                 mostrarLoader(false);
            return;
        }

        if (incompletos === 0) {
            mostrarMensaje("✅ Todos los productos están completos. Transferencia creada.");
        } else {
            const confirmar = confirm("⚠️ Hay productos incompletos. ¿Deseas continuar?");
            if (confirmar) {
                const clave = prompt("🔐 Ingresa la clave para continuar:");
                if (clave === "12345") {
//mostrarMensaje("🔓 Clave correcta. Transferencia creada con productos incompletos.");

                // Construir JSON de transferencia
                const stockTransfer = {
                    cardCode: "",
                    comments: "sc22",
                    fromWarehouse: "<?= $TEMPa1->Filler ?>",
                    toWarehouse: "<?= $TEMPa1->ToWhsCode ?>",
                    priceList: -2,
                    stockTransferLines: []
                };

                filas.forEach(fila => {
                    const escaneado = parseInt(fila.cells[3].textContent);
                    if (escaneado > 0) {
                        stockTransfer.stockTransferLines.push({
                            itemCode: fila.getAttribute("data-itemcode"),
                            quantity: escaneado,
                            warehouseCode: "<?= $TEMPa1->ToWhsCode ?>",
                            baseEntry: parseInt(fila.getAttribute("data-docentry")),
                            baseLine: parseInt(fila.getAttribute("data-linenum")),
                            baseType: 1250000001
                        });
                    }
                });
                 const jsonBlob = new Blob([JSON.stringify(stockTransfer, null, 2)], { type: "application/json" });
                const url = URL.createObjectURL(jsonBlob);
                const a = document.createElement("a");
                 console.log("ap"),
                a.href = url;
                a.download = "transferencia.json";
           /*     document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);*/

                

                    const jsonPayload = JSON.stringify(stockTransfer);
         fetch("php/enviar_transferencia.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify(stockTransfer)
    })
    .then(async res => {
    const text = await res.text();

    try {
        const jsonMatch = text.match(/{[\s\S]*}/);
        const data = jsonMatch ? JSON.parse(jsonMatch[0]) : {};
        const mensaje = data?.error?.message?.value || data?.message?.value || text;

        console.log("📦 Mensaje recibido:", mensaje);

        if (mensaje.includes("Transferencia creada")) {
        alert("✅ Transferencia creada correctamente.");
        } else {
        alert("⚠️ Algo ocurrió: " + mensaje);
        // Aquí puedes ejecutar otra acción si no fue creada
        }
        //mostrarLoader(false);
    } catch (e) {
        console.warn("⚠️ No se pudo procesar la respuesta:", text);
        alert("⚠️ Respuesta inesperada del servidor:\n" + text);
        //mostrarLoader(false);
    } finally {
        
    }
    })
    .catch(error => {
    
    console.error("❌ Error al enviar:", error);
    alert("❌ Error al enviar la transferencia.");
    //mostrarLoader(false);
    });


   

     

    console.log("📦 JSON generado:", stockTransfer);
    alert("📦 JSON generado. Revisa la consola para ver el contenido.");
                    


                    } else {
                    alert("❌ Clave incorrecta. Operación cancelada.");
                }
            }
        }
        
     // Al final de todo:
     //mostrarLoader(false);

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


function mostrarMensaje(texto) {
     const mensaje = document.getElementById('mensajeSimple');
     mensaje.textContent = texto;
     mensaje.style.display = 'block';
     setTimeout(() => {
     mensaje.style.display = 'none';
     }, 3000);
}


</script>

<?php include_once "footer.php"; ?>
