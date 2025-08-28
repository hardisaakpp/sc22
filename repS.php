<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";
include_once "php/bd_desarrollo.php";

// Verificar almacenes asignados
if (empty($_SESSION["whsCica"]) || empty($_SESSION["whsTr"]) || $_SESSION["whsCica"] <= 0 || $_SESSION["whsTr"] <= 0) {
    echo '<div class="alert alert-danger text-center">No tiene almacenes asignados</div>';
    exit;
}

// Obtener nombre del almacén
$whsCica = $_SESSION["whsCica"] ?? null;
$alm = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$alm->execute([$whsCica]);
$almacen = $alm->fetch(PDO::FETCH_OBJ);

// Obtener nombre del almacén
$whsTr = $_SESSION["whsTr"] ?? null;
$almt = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$almt->execute([$whsTr]);
$almTr = $almt->fetch(PDO::FETCH_OBJ);

// Obtener nombre del almacén
$whsCD = $_SESSION["whsCD"] ?? null;
$almcd = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$almcd->execute([$whsCD]);
$almCD = $almcd->fetch(PDO::FETCH_OBJ);

// Ejecutar procedimiento almacenado (no devuelve nada)
$idUser = $_SESSION["idU"] ?? null;
$idRepCab = null;
if ($almTr && $idUser) {
    $stmtProc = $db->prepare("EXEC sp_GetOrInsert_RepCab ?, ?, ?");
    $stmtProc->execute([$almTr->cod_almacen, $idUser, $almCD->cod_almacen]);

    // Obtener el id por consulta con ToWhs y fecha actual
    $fechaHoy = date('Y-m-d');
    $stmtId = $db->prepare("SELECT TOP 1 id FROM [STORECONTROL].[dbo].[rep_cab] WHERE ToWhs = ? AND idUser = ? AND CAST(fecCreacion AS DATE) = ?");
    $stmtId->execute([$almTr->cod_almacen, $idUser, $fechaHoy]);
    $resultId = $stmtId->fetch(PDO::FETCH_ASSOC);
    if ($resultId && isset($resultId['id'])) {
        $idRepCab = $resultId['id'];
        //echo "idRepCab obtenido: " . $idRepCab ;
    }
}

// Obtener datos de pedido
$sol = $db->prepare("SELECT TOP (1) * FROM [STORECONTROL].[dbo].[rep_cab]
  where id = ?");
$sol->execute([$idRepCab]);
$solcab = $sol->fetch(PDO::FETCH_OBJ);

// Consulta de carrito
$sql = "DECLARE @WHS nvarchar(10);
        SET @WHS = '".$almacen->cod_almacen."';
        DECLARE @TOWHS nvarchar(10);
        SET @TOWHS = '".$almTr->cod_almacen."';

        DECLARE @IDCAB int;
        SET @IDCAB = (
            SELECT TOP 1 id
            FROM [LS_10_10_100_12_Prod].[STORECONTROL].[dbo].rep_cab
            WHERE CAST(fecCreacion AS DATE) = CAST(GETDATE() AS DATE)
            AND [ToWhs] = @TOWHS
            ORDER BY fecCreacion DESC  -- opcional, por si hay varios en el mismo día
        );

        SELECT *, sugerido_final AS Sugerido
        FROM [LS_10_10_100_12_Prod].[STORECONTROL].[dbo].[rep_det] d
            join [MODULOS_SC].[reposicion].[ProcesadosCache] c on d.ItemCode=c.ItemCode
        where	d.[fk_id_cab]= @IDCAB AND d.Quantity>0 AND c.WhsCode=@WHS;";

$stmt = $dbdev->prepare($sql);
$stmt->execute();
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);

?>




<div class="content">
    <div class="col-md-6 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Solicitud de transferencia</strong>           
            </div>  
            <div class="card-body">
                <form method="GET" action="" id="form-filtros">
                    <table style="width:50%; border-collapse: collapse;">
                        <tr>
                            <td style="padding:4px; font-weight:bold;">Origen:</td>
                            <td style="padding:4px;"><?php echo $solcab->FromWhs; ?></td>
                        </tr>
                        <tr>
                            <td style="padding:4px; font-weight:bold;">Destino:</td>
                            <td style="padding:4px;"><?php echo $solcab->ToWhs; ?></td>
                        </tr>
                        <tr>
                            <td style="padding:4px; font-weight:bold;">Fecha:</td>
                            <td style="padding:4px;"><?php echo date("d-m-Y", strtotime($solcab->fecCreacion)); ?></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

    
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Detalle</strong>
            </div>
            <div class="card-body">
                <table id="data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">Código Barras</th>
                            <th>Descripción</th>
                            <th class="d-none d-md-table-cell">Embalaje</th>
                            <th class="d-none d-md-table-cell">Stock Tienda</th>
                            <th class="d-none d-md-table-cell">Tránsito</th>
                            <th class="d-none d-md-table-cell">Comprometido</th>
                            <th>Total Disponible</th>
                            <th class="d-none d-md-table-cell col-dispo-bodega">Stock Bodega</th>
                            <th>Disponible Bodega</th>
                            <th class="d-none d-md-table-cell"></br></br>Total Venta 30 días</th>
                            <th>Sugerido</th>
                            <th>Solicitado</th>
                            <th class="d-none d-md-table-cell">Días de Inv.</th>
                            <th class="d-none d-md-table-cell">Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen as $r): ?>
        <?php
             $valorSolicitado = isset($solicitados[$r->ItemCode]) ? $solicitados[$r->ItemCode] : 0;
            $comentario = isset($comentarios[$r->ItemCode]) ? $comentarios[$r->ItemCode] : '';
            $solicitadosTiendas = isset($solicitadosTiendas[$r->ItemCode]) ? $solicitadosTiendas[$r->ItemCode] : 0;
            $transito = floatval($r->total_Transitoria_Tienda);
            $onhand = floatval($r->OnHand);
            $ventas = floatval($r->VentaPromedio);
            $comprometido = floatval($r->total_Solicitud_Tienda);
            $solicitado = floatval($valorSolicitado);
            $totalDisponible = $onhand + $transito + $solicitado +$comprometido;
            if ($ventas > 0) {
                if ($solicitado == 0) {
                    $diasInv = round((($transito + $onhand + $solicitado) / $ventas) );
                } else {
                    $diasInv = round((($transito + $onhand + $solicitado) / $ventas) );
                }
            } else {
                $diasInv = 0;
            }
        ?>
        <tr>
             <td class="d-none d-md-table-cell"><?= $r->CodeBars ?></td>
            
            <td><?= $r->ItemCode.' - '.$r->ItemName ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->embalaje,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->OnHand,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->total_Transitoria_Tienda,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->total_Solicitud_Tienda,0) ?></td>
            <td class="total-disponible"><?= number_format($totalDisponible,0) ?></td>
            <td class="col-dispo-bodega"><?= number_format($r->total_Bodega,0) ?></td>
            <td><?= number_format($r->total_Bodega-$solicitadosTiendas,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->CantidadTotalTreintaDias,0) ?></td>
            <td><?= number_format($r->Sugerido,0) ?></td>
            <td><?= number_format($r->Quantity,0) ?></td>
            <td class="dias-inv"><?= number_format($diasInv, 2) ?></td>
            <td><?= $r->comment ?></td>
         
        </tr>
    <?php endforeach; ?>
    <?php if (empty($resumen)): ?>
        <tr><td colspan="13" class="text-center">No hay datos</td></tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script>
$(document).ready(function() {

  
    // ---------------------------
    // Validar al salir del input solicitado
    // ---------------------------
    $('#data-table tbody').on('blur', 'input[type="number"][name^="solicitar"]', function() {
        const sugerido = parseFloat($(this).data('sugerido'));
        let value = parseFloat(this.value);
        const original = $(this).data('original');
        const itemcode = $(this).attr('name').match(/\[(.*?)\]/)[1];
        const towhs = "<?= $almTr->cod_almacen ?>";
        const idcab = "<?= $idRepCab ?>";
        const stockBodega = parseFloat($(this).closest('tr').find('td').eq(6).text().replace(/,/g, ''));

        // Para cálculo de días de inventario
        let transito = parseFloat($(this).data('transito')) || 0;
        let onhand = parseFloat($(this).data('onhand')) || 0;
        let ventas = parseFloat($(this).data('ventas')) || 0;
        let $diasInvTd = $(this).closest('tr').find('td.dias-inv');
        let solicitado = value;

        if (isNaN(value) || value < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Valor inválido',
                text: 'El valor no puede ser menor que 0',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                this.value = original;
                solicitado = parseFloat(original) || 0;
                // Recalcular días de inventario
                let diasInv = 0;
                if (ventas > 0) {
                    if (solicitado == 0) {
                        diasInv = ((solicitado + transito + onhand) / ventas) * 30;
                    } else {
                        diasInv = ((transito + onhand + solicitado) / ventas) * 30;
                    }
                }
                $diasInvTd.text(diasInv.toFixed(2));
            });
            return;
        }

        if (value > stockBodega) {
            Swal.fire({
                icon: 'error',
                title: 'Stock insuficiente',
                text: 'No puede solicitar más que el Stock Bodega (' + stockBodega + ')',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                this.value = original;
                solicitado = parseFloat(original) || 0;
                // Recalcular días de inventario
                let diasInv = 0;
                if (ventas > 0) {
                    if (solicitado == 0) {
                        diasInv = ((solicitado + transito + onhand) / ventas) * 30;
                    } else {
                        diasInv = ((transito + onhand + solicitado) / ventas) * 30;
                    }
                }
                $diasInvTd.text(diasInv.toFixed(2));
            });
            return;
        }

        if (value > sugerido) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad mayor a la sugerida',
                html: `Se sugiere solicitar <b>${sugerido}</b>.<br>¿Desea solicitar <b>${value}</b>?`,
                showCancelButton: true,
                confirmButtonText: 'Sí, solicitar',
                cancelButtonText: 'No, usar sugerido'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).data('original', value);
                    // Guardar con AJAX
                    $.ajax({
                        url: 'ajax_repdet.php',
                        type: 'POST',
                        data: {
                            idcab: idcab,
                            towhs: towhs,
                            itemcode: itemcode,
                            quantity: value
                        },
                        success: function(resp) {
                            console.log("Guardado:", resp);
                        },
                        error: function(xhr) {
                            console.error("Error al guardar:", xhr.responseText);
                        }
                    });
                    // Recalcular días de inventario con el valor confirmado
                    solicitado = value;
                    let diasInv = 0;
                    if (ventas > 0) {
                        diasInv = ((transito + onhand + solicitado) / ventas) * 30;
                    }
                    $diasInvTd.text(diasInv.toFixed(2));
                } else {
                    this.value = sugerido;
                    $(this).data('original', sugerido);
                    solicitado = sugerido;
                    // Recalcular días de inventario con el valor sugerido
                    let diasInv = 0;
                    if (ventas > 0) {
                        diasInv = ((transito + onhand + solicitado) / ventas) * 30;
                    }
                    $diasInvTd.text(diasInv.toFixed(2));
                }
            });
        } else {
            $(this).data('original', value);
            // Guardar con AJAX
            $.ajax({
                url: 'ajax_repdet.php',
                type: 'POST',
                data: {
                    idcab: idcab,
                    towhs: towhs,
                    itemcode: itemcode,
                    quantity: value
                },
                success: function(resp) {
                    console.log("Guardado:", resp);
                },
                error: function(xhr) {
                    console.error("Error al guardar:", xhr.responseText);
                }
            });
            // Recalcular días de inventario con el valor aceptado
            solicitado = value;
            let diasInv = 0;
            if (ventas > 0) {
                if (solicitado == 0) {
                    diasInv = ((solicitado + transito + onhand) / ventas) * 30;
                } else {
                    diasInv = ((transito + onhand + solicitado) / ventas) * 30;
                }
            }
            $diasInvTd.text(diasInv.toFixed(2));
        }
    });

    // Guardar comentario al salir del input observaciones
    $('#data-table tbody').on('blur', 'input[type="text"][name^="comment"]', function() {
        const value = $(this).val();
        const itemcode = $(this).attr('name').match(/\[(.*?)\]/)[1];
        const towhs = "<?= $almTr->cod_almacen ?>";
        const idcab = "<?= $idRepCab ?>";
        // Guardar con AJAX igual que solicitado
        $.ajax({
            url: 'ajax_repdet.php',
            type: 'POST',
            data: {
                idcab: idcab,
                towhs: towhs,
                itemcode: itemcode,
                comment: value
            },
            success: function(resp) {
                console.log("Comentario guardado:", resp);
            },
            error: function(xhr) {
                console.error("Error al guardar comentario:", xhr.responseText);
            }
        });
    });

    // ---------------------------
    // Botón Limpiar filtros
    // ---------------------------
    $('#btnLimpiar').click(function() {
        $('#form-filtros')[0].reset();
        table.columns().search('').draw(); // limpiar filtros de DataTable
    });

    // Actualizar días de inventario al cambiar el input
    $('#data-table tbody').on('input blur', 'input[type="number"][name^="solicitar"]', function() {
        let solicitado = parseFloat(this.value) || 0;
        let transito = parseFloat($(this).data('transito')) || 0;
        let onhand = parseFloat($(this).data('onhand')) || 0;
        let ventas = parseFloat($(this).data('ventas')) || 0;
        let $diasInvTd = $(this).closest('tr').find('td.dias-inv');
        let diasInv = 0;
        if (ventas > 0) {
            if (solicitado == 0) {
                diasInv = ((solicitado + transito + onhand) / ventas) * 30;
            } else {
                diasInv = ((transito + onhand + solicitado) / ventas) * 30;
            }
        }
        $diasInvTd.text(diasInv.toFixed(2));
    });

    // Acción botón modal cod-producto (consulta AJAX y muestra datos con SweetAlert2)
    $(document).on('click', '.btn-modal-codprod', function() {
        var codprod = $(this).data('codprod');
        var whscode = "<?= $almacen->cod_almacen ?>";
        var solicitado = $(this).closest('tr').find('td').eq(9).text();
        var totalBodega = $(this).closest('tr').find('td').eq(6).text().replace(/,/g, '');
        var totalTransitoriaBodega = $(this).closest('tr').find('td').eq(7).text().replace(/,/g, '');
        var totalStock = (parseFloat(solicitado) || 0) + (parseFloat(totalBodega) || 0) + (parseFloat(totalTransitoriaBodega) || 0);

        $.ajax({
            url: 'ajax_modal_itemcode.php',
            type: 'POST',
            dataType: 'json',
            data: {
                itemcode: codprod,
                whscode: whscode
            },
            success: function(resp) {
                if (resp && resp.success && resp.data) {
                    var d = resp.data;
                    var html = `
                        <div style="font-size:12px;">
                        <table class="table table-bordered table-sm" style="margin-bottom:0;">
                            <tr>
                                <td><b>CodeBars</b></td><td>${d.CodeBars}</td>
                                <td><b>ItemCode</b></td><td>${d.ItemCode}</td>
                            </tr>
                            <tr>
                                <td><b>ItemName</b></td><td colspan="3">${d.ItemName}</td>
                            </tr>
                            <tr>
                                <td><b>ClasificacionABC</b></td><td>${d.ClasificacionABC}</td>
                                <td><b>Unidad</b></td><td>${d.unidad}</td>
                            </tr>
                            <tr>
                                <td><b>Categoria</b></td><td>${d.categoria}</td>
                                <td><b>Linea</b></td><td>${d.linea}</td>
                            </tr>
                            <tr>
                                <td><b>Marca</b></td><td>${d.marca}</td>
                                <td><b>Ult. Fecha Ingreso</b></td><td>${d.ultima_fecha_ingreso}</td>
                            </tr>
                            <tr>
                                <td><b>Días Ult. Fecha Ingreso</b></td><td>${d.dias_ultima_fecha_ingreso}</td>
                                <td><b>Venta Ultima</b></td><td>${d.VentaUltima}</td>
                            </tr>
                            <tr>
                                <td><b>PromVenta30dias</b></td><td>${d.PromVenta30dias}</td>
                                <td><b>Venta 90 días</b></td><td>${d.venta_90dias}</td>
                            </tr>
                            <tr>
                                <td><b>PromVenta90dias</b></td><td>${d.PromVenta90dias}</td>
                                <td><b>OnHand</b></td><td>${d.OnHand}</td>
                            </tr>
                            <tr>
                                <td><b>Días Inv Actual</b></td><td>${d.diasInvActual}</td>
                                <td><b>Total Bodega</b></td><td>${d.total_Bodega}</td>
                            </tr>
                            <tr>
                                <td><b>Total Transitoria Bodega</b></td><td>${d.total_Transitoria_Bodega}</td>
                                <td><b>MinStock</b></td><td>${d.MinStock}</td>
                            </tr>
                            <tr>
                                <td><b>MaxStock</b></td><td>${d.MaxStock}</td>
                                <td><b>U_LEAD</b></td><td>${d.U_LEAD}</td>
                            </tr>
                            <tr>
                                <td><b>Solicitado</b></td><td>${solicitado}</td>
                                <td><b>TOTALSTOCK</b></td><td>${totalStock}</td>
                            </tr>
                        </table>
                        </div>
                    `;
                    Swal.fire({
                        title: 'Detalle Producto',
                        html: html,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Detalle Producto',
                        text: 'No se encontraron datos.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo consultar el producto.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

});
</script>

<!-- Modal -->
<div class="modal fade" id="modalCodProd" tabindex="-1" role="dialog" aria-labelledby="modalCodProdLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCodProdLabel">Código Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalCodProdBody">
        <!-- Aquí se muestra el código producto -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<div class="text-right mb-3">
    <button id="btnDescargarPDF" class="btn btn-danger">
        Descargar PDF
    </button>
</div>
<script>
document.getElementById("btnDescargarPDF").addEventListener("click", function() {
    const content = document.querySelector(".content");

    html2canvas(content, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF("p", "mm", "a4");

        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        const imgWidth = pageWidth;
        const imgHeight = canvas.height * imgWidth / canvas.width;

        // --------------------------
        // Encabezado dinámico
        // --------------------------
        const fechaHora = "<?= date('d-m-Y H:i:s'); ?>";
        const almacen = "<?= $almacen->cod_almacen ?? 'N/A'; ?>";
        const usuario = "<?= $_SESSION['idU'].' - '.$_SESSION['username']; ?>";

        pdf.setFontSize(10);
        pdf.text("Fecha y Hora: " + fechaHora, 10, 10);
        pdf.text("Almacén: " + almacen, 10, 15);
        pdf.text("Usuario: " + usuario, 10, 20);

        // --------------------------
        // Imagen del contenido
        // --------------------------
        let position = 30; // deja espacio al encabezado

        if (imgHeight < pageHeight - 30) {
            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
        } else {
            let heightLeft = imgHeight;
            while (heightLeft > 0) {
                pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
                if (heightLeft > 0) {
                    pdf.addPage();
                    // Reagregar encabezado en cada página
                    pdf.setFontSize(10);
                    pdf.text("Fecha y Hora: " + fechaHora, 10, 10);
                    pdf.text("Almacén: " + almacen, 10, 15);
                    pdf.text("Usuario: " + usuario, 10, 20);
                    position = 30;
                }
            }
        }

        pdf.save("solicitud_transferencia.pdf");
    });
});
</script>


<!-- jsPDF + html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<?php include_once "footer.php"; ?>
