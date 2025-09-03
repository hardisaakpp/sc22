<?php

// Ahora sí es seguro incluir headers porque no hemos enviado salida aún
include_once "header.php";


    $fecha = $_GET["fecha"] ?? $_POST["fecha"] ?? null;
    $whsCode = $_GET["whsCode"] ?? $_POST["whsCode"] ?? null;
 $id = $whsCode.$fecha;

    // 1. Obtener depósitos
    $sql1 = "
        SELECT d.Id,
            d.U_WhsCode,
            d.U_Fecha AS FechaCierre,
            d.DepositDate,
            c.FormatCode,
            c.AcctName,
            d.U_Ref_Bancar,
            d.TotalLC,
            d.creadoSAP AS integrado,
            d.sobrante
        FROM DepositosTiendas d
        JOIN CuentaFinanciera c ON d.DepositAccount = c.AcctCode
        WHERE d.U_WhsCode = ? AND d.U_Fecha = ?
    ";
    $stmt1 = $db->prepare($sql1);
    $stmt1->execute([$whsCode, $fecha]);
    $depositos = $stmt1->fetchAll(PDO::FETCH_OBJ);

    // 2. Total de depósitos
    $totalDepositos = 0;
    foreach ($depositos as $d) {
        $totalDepositos += $d->TotalLC;
    }

    // 3. Obtener efectivo desde cicUs
    $sql2 = "
        SELECT SUM(c.valRec) AS Efectivo
        FROM cicUs c
        WHERE c.whsCode = ? AND c.fecha = ?
        AND (c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%'
            OR c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%')
    ";
    $stmt2 = $db->prepare($sql2);
    $stmt2->execute([$whsCode, $fecha]);
    $efectivo = $stmt2->fetch(PDO::FETCH_OBJ)->Efectivo ?? 0;

    // 4. Calcular diferencia
    $diferencia = $totalDepositos - $efectivo;
    $clase = ($diferencia == 0) ? 'success' : 'danger';
?>


<style>
   
    @media screen {
        .solo-impresion {
            display: none;
        }
    }
    @media print {
        body * {
            visibility: hidden;
        }

        #areaImprimir, #areaImprimir * {
            visibility: visible;
        }

        #areaImprimir {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-imprimir {
            display: none !important;
        }
        .solo-impresion {
        display: block;
        text-align: right;
        font-size: 12px;
        margin-top: 10px;
    }
    }
</style>




<div class="content"> <div id="areaImprimir">
<div class="solo-impresion" id="fechaImpresion">
    Fecha de impresión: <span id="fechaActual"></span>
</div>
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Detalle de Depósitos - <?= $whsCode ?> - <?= $fecha ?></strong>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                           <th>ID</th>
                            <th>Fecha Cierre</th>
                            <th>Fecha Depósito</th>
                            <th>Cuenta</th>
                            <th>Nombre Cuenta</th>
                            <th>Referencia Bancaria</th>
                            <th>Total</th>
                    
                            <th>Integrado SAP</th>
                            <th>Acción</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($depositos as $d): ?>
                            <tr>
                                <td><?= $d->Id ?></td>
                                <td><?= $d->FechaCierre ?></td>
                                <td><?= $d->DepositDate ?></td>
                                <td><?= $d->FormatCode ?></td>
                                <td><?= $d->AcctName ?></td>
                                <td><?= $d->U_Ref_Bancar ?></td>
                                <td><?= number_format($d->TotalLC, 2) ?></td>
                     

                                <td><?= $d->integrado ? 'Sí' : 'No' ?></td>
                                <td>
                                    <?php if (!$d->integrado): ?>


                                        <button class="btn btn-sm btn-success" onclick='mostrarModalIntegracion(<?= json_encode($d) ?>)'>
                                            Integrar
                                        </button>


                                    <a href="php/eliminar_deposito.php?id=<?= $d->Id ?>&U_Fecha=<?= urlencode($fecha) ?>&U_WhsCode=<?= urlencode($whsCode) ?>"
                                    onclick="return confirm('¿Estás seguro de eliminar este registro?')"
                                    class="btn btn-danger btn-sm">
                                    Eliminar
                                    </a>


                                    <?php else: ?>
                                        <span class="text-success">✔</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        <?php if (empty($depositos)): ?>
                            <tr><td colspan="7" class="text-center">No hay depósitos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total Depósitos:</th>
                            <th colspan="2"><?= number_format($totalDepositos, 2) ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Efectivo (cicUs):</th>
                            <th colspan="2"><?= number_format($efectivo, 2) ?></th>
                        </tr>
                        <tr class="table-<?= $clase ?>">
                            <th colspan="5" class="text-right">Diferencia:</th>
                            <th colspan="2"><?= number_format($diferencia, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
                   <?php if ((isset($_GET['pflag']) && $_GET['pflag'] == 1)): ?>
                        <button class="btn btn-success mt-3" onclick="window.close()">OK</button>
                    <?php else: ?>
                        <a href="depL.php" class="btn btn-outline-secondary mt-3">🔙 Volver al resumen</a>
                        <a href="depC.php?U_Fecha=<?= $fecha ?>" class="btn btn-primary mt-3">📄 Nuevo</a>
                        <button onclick="imprimirContenido()" class="btn btn-primary mt-3">🖨️ Imprimir</button>
                        
                    <?php endif; ?>
                        <button type="button" class="btn btn-warning mt-3" onClick=window.open("<?php echo "adjuntos.php?id=" . $id ?>","demo","toolbar=0,status=0,")>
                            <i class="fa fa-paperclip"></i>&nbsp; ADJUNTOS
                        </button>


            </div>
        </div>
    </div>
</div>
</div>
<!-- Modal de Confirmación de Integración (Bootstrap 4.3) -->
<div id="modalIntegracion" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1050;">
  <div style="background:#fff; margin:5% auto; padding:25px 30px; width:500px; border-radius:10px; box-shadow: 0 0 10px #888;">
    <h5 style="text-align:center; font-weight:bold; margin-bottom:15px;">🧾 Confirmación de Integración</h5>
    <p style="font-size:16px;">¿Está seguro que desea integrar este depósito con los siguientes datos?</p>
    <table class="table table-bordered" style="font-size:15px;">
      <tr><th>Fecha de Depósito</th><td id="datoDepositDate" class="font-weight-bold text-primary"></td></tr>
      <tr><th>Cuenta</th><td><span id="datoFormatCode" class="font-weight-bold text-dark"></span> - <span id="datoAcctName" class="font-weight-bold text-dark"></span></td></tr>
      <tr><th>N° Comprobante</th><td id="datoRefBancar" class="font-weight-bold text-danger"></td></tr>
      <tr><th>Valor</th><td id="datoTotalLC" class="font-weight-bold text-success"></td></tr>
    </table>

    <div class="text-right mt-3">
      <button type="button" class="btn btn-secondary" onclick="cerrarModalIntegracion()">Cancelar</button>
      <button type="button" class="btn btn-success" id="btnConfirmarIntegrar">✅ Confirmar e Integrar</button>
    </div>
  </div>
</div>



<!-- Loading Overlay -->
<div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.8); z-index:2000; align-items:center; justify-content:center; font-size:1.2rem;">
    <div style="text-align:center;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <div class="mt-3 font-weight-bold">Espere... se está integrando el depósito</div>
    </div>
</div>


<script>


function mostrarModalIntegracion(d) {
    document.getElementById('datoDepositDate').innerText = d.DepositDate;
    document.getElementById('datoFormatCode').innerText = d.FormatCode;
    document.getElementById('datoAcctName').innerText = d.AcctName;
    document.getElementById('datoRefBancar').innerText = d.U_Ref_Bancar;
    document.getElementById('datoTotalLC').innerText = Number(d.TotalLC).toLocaleString('es-EC', { minimumFractionDigits: 2 });

    // Puedes usar estos datos para después enviar por POST o AJAX
document.getElementById('btnConfirmarIntegrar').onclick = function () {
    cerrarModalIntegracion(); // Opcional: cerrar modal al confirmar
    integrarDepositoPorId(d.Id);
};


    document.getElementById('modalIntegracion').style.display = 'block';
}

function cerrarModalIntegracion() {
    document.getElementById('modalIntegracion').style.display = 'none';
}



    function eliminarRegistro(id) {
        if (confirm("¿Está seguro de eliminar este registro?")) {
            window.location.href = "depD.php?eliminar=" + id;
        }
    }



    document.addEventListener("DOMContentLoaded", function () {
        const fecha = new Date();
        const opciones = { year: 'numeric', month: '2-digit', day: '2-digit', 
                        hour: '2-digit', minute: '2-digit' };
        document.getElementById("fechaActual").textContent = fecha.toLocaleString('es-EC', opciones);
    });

    function imprimirContenido() {
        var contenido = document.getElementById('areaImprimir').innerHTML;
        var ventana = window.open('', '', 'height=800,width=1000');
        ventana.document.write('<html><head><title>Imprimir</title>');
        ventana.document.write('<style>');
        ventana.document.write('table { border-collapse: collapse; width: 100%; font-family: Arial; }');
        ventana.document.write('th, td { border: 1px solid #333; padding: 6px 8px; text-align: center; }');
        ventana.document.write('th { background-color: #f0f0f0; }');
        ventana.document.write('</style></head><body>');
        ventana.document.write(contenido);
        ventana.document.write('</body></html>');
        ventana.document.close();
        ventana.print();
    }

    function integrarDeposito(whsCode, fecha, total) {
        if (confirm(`¿Estás seguro de integrar el depósito del ${fecha} (${whsCode}) por $${total}?`)) {
            fetch('enviar_deposito_sap.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ whsCode, fecha })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Depósito integrado correctamente.');
                    location.reload();
                } else {
                    alert('Error al integrar: ' + (data.error || 'Respuesta inesperada'));
                }
            })
            .catch(err => alert('Error de red: ' + err));
        }
    }


function integrarDepositoPorId(id) {
    document.getElementById('loadingOverlay').style.display = 'flex'; // Mostrar overlay

    fetch('enviar_deposito_sap.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('loadingOverlay').style.display = 'none'; // Ocultar overlay
        if (data.success) {
            alert('Depósito integrado correctamente.');
            location.reload();
        } else {
            alert('Error al integrar: ' + (data.error || 'Respuesta inesperada'));
        }
    })
    .catch(err => {
        document.getElementById('loadingOverlay').style.display = 'none'; // Ocultar overlay
        alert('Error de red: ' + err);
    });
}



</script>


<?php include_once "footer.php"; ?>
