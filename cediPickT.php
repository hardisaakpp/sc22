<?php
include_once "header.php";

if (!isset($_GET["idcab"])) {
    exit();
}
$idcab = $_GET["idcab"];

$s11 = $db->query("
	SELECT g.[id]
      ,g.[fk_idgroup]
      ,g.[estado]
      ,g.[fk_docnumsotcab]
      ,g.[enabled] as Enabledx
	  ,g.ToWhsCode
	  ,g.DocDate , g.Filler
  FROM [dbo].[ced_groupsot] g 
            WHERE g.id = ".$idcab." ");
$TEMPa1 = $s11->fetchObject();
       $ToWhsCode = $TEMPa1->ToWhsCode;
       $Enable = $TEMPa1->Enabledx;
       $idgrupo = $TEMPa1->fk_idgroup;
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
<?php
    if ($Enable == 1) {
        echo ' <form id="form-codigo">
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
            ';
    }
?>
           




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

<?php
    if ($Enable == 1) {
        echo ' <button id="btnTransferencia" class="btn btn-warning">Crear Transferencia</button>
             <button id="btnActualizarSolicitud" class="btn btn-info">Actualizar Solicitud</button>
            ';
    }
?>

               
                
               

<button id="btnDescargarCSV" class="btn btn-outline-info mt-2">📥 Exportar</button>
<a href="cediGrpLdid.php?idcab=<?= $idgrupo ?>" class="btn btn-secondary mt-2">⬅️ Volver</a>





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

    document.getElementById('form-codigo').addEventListener('submit', function(e) {
  e.preventDefault();
  const codigoIngresado = document.getElementById('codigo').value.trim();
  const unidades = parseInt(document.getElementById('txtu').value) || 1;
  if (!codigoIngresado || unidades < 1) return;

  const filas = document.querySelectorAll(`tr[data-codigo="${codigoIngresado}"]`);
  let escaneado = false;

  for (let fila of filas) {
    const escaneadosCelda = fila.querySelector('.escaneados');
    const cantidadActual = parseInt(escaneadosCelda.textContent) || 0;
    const stock = parseInt(fila.children[2].textContent) || 0;

    if (cantidadActual < stock) {
      const nuevaCantidad = Math.min(cantidadActual + unidades, stock);
      escaneadosCelda.textContent = nuevaCantidad;

      fila.classList.remove('fila-completa', 'fila-parcial');
      if (nuevaCantidad === stock) {
        fila.classList.add('fila-completa');
      } else {
        fila.classList.add('fila-parcial');
      }

      document.querySelector('#tabla tbody').prepend(fila);
      escaneado = true;
      break; // ✅ Solo escanea una línea por vez
    }
  }

  if (!escaneado) {
    alert(`El código ${codigoIngresado} ya ha sido completado en todas sus líneas.`);
  }

  document.getElementById('txtu').value = 1;
  document.getElementById('codigo').value = '';
  document.getElementById('codigo').focus();
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

    fetch('php/guardar_scans.php', {
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
             mostrarLoader(false);
        } else {
            alert("❌ Error al guardar los datos.");
             mostrarLoader(false);
        }
    })
    .catch(error => {
        
        console.error("Error:", error);
        alert("❌ Error en la comunicación con el servidor.");
        mostrarLoader(false);
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
     // Verificar si hay productos escaneados
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
        if (totalEscaneado === 0) {
            alert("⚠️ No se ha escaneado ningún producto. No se puede crear la transferencia.");
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

    // Crear transferencia 


            let continuar = false;


                if (incompletos === 0) {
                    continuar = true;
                    console.log("✅ Todos los productos están completos. Continuando con la transferencia...");
                }else if ((incompletos > 0 && confirm("⚠️ Hay productos incompletos. ¿Deseas continuar?"))) {                     
                    const clave = prompt("🔐 Ingresa la clave para continuar:");
                    if (clave !== "12345") {
                        alert("❌ Clave incorrecta. Operación cancelada.");
                        mostrarLoader(false);
                        return;
                    }else {
                        console.log("🔓 Clave correcta. Continuando con la transferencia...");
                        continuar = true;
                    }
                }else{
                    alert("⚠️ Transferencia cancelada por el usuario.");
                    mostrarLoader(false);
                    return;
                }

                if(continuar){
                        const jsonPayload = JSON.stringify(stockTransfer);
                        fetch("php/enviar_transferencia.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: jsonPayload
                        })
                        .then(async response => {
                            const text = await response.text();
                            mostrarLoader(false);
                            try {
                                const jsonMatch = text.match(/{[\s\S]*}/);
                                const data = jsonMatch ? JSON.parse(jsonMatch[0]) : {};
                                const mensaje = data?.error?.message?.value || data?.message?.value || text;
                                console.log("📦 Mensaje recibido:", mensaje);
                                if (mensaje.includes("Transferencia creada")) {
                                        fetch("php/actualizar_estado.php", {
                                            method: "POST",
                                            headers: {
                                            "Content-Type": "application/x-www-form-urlencoded"
                                            },
                                            body: new URLSearchParams({
                                            fk_idgroup: "<?= $TEMPa1->fk_idgroup ?>",
                                            docnum_sot: "<?= $TEMPa1->id ?>"
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            console.log("📝 Estado actualizado:", data.message);
                                        })
                                        .catch(err => {
                                            console.error("❌ Error al actualizar estado:", err);
                                        });

                                    alert("✅ Transferencia creada correctamente.");
                                    //window.location.href = "cediGrpLdid.php?idcab=<?= $TEMPa1->fk_idgroup ?>";
                                         mostrarLoader(false);
                                         window.location.href = "cediGrpLdis.php";
                                } else {
                                    alert("⚠️ Algo ocurrió: " + mensaje);
                                         mostrarLoader(false);
                                }
                            } catch (e) {
                                console.warn("⚠️ Respuesta no JSON:", text);
                                alert("⚠️ Respuesta inesperada del servidor:\n" + text);
                            }
                        })
                        .catch(error => {
                            console.error("❌ Error al enviar:", error);
                            alert("❌ Error al enviar la transferencia.");
                            mostrarLoader(false);
                        });
                        console.log("📦 JSON generado:", stockTransfer);
                }
 


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

document.getElementById('btnActualizarSolicitud').addEventListener('click', function () {
    if (!confirm("¿Estás seguro de que deseas actualizar la solicitud?")) return;

    mostrarLoader(true);

    fetch('php/actualizar_solicitud.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            fk_docnumsotcab: '<?= $TEMPa1->fk_docnumsotcab ?>',
            fk_idgroup: '<?= $TEMPa1->fk_idgroup ?>'
        })
    })
    .then(res => res.json())
    .then(data => {
        mostrarLoader(false);
        alert(data.message || "✅ Solicitud actualizada correctamente.");
        location.reload();
    })
    .catch(err => {
        mostrarLoader(false);
        console.error("❌ Error al actualizar solicitud:", err);
        alert("❌ Error al actualizar la solicitud.");
    });
});



</script>

<?php include_once "footer.php"; ?>
