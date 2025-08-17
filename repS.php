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

// Ejecutar procedimiento almacenado (no devuelve nada)
$idUser = $_SESSION["idU"] ?? null;
$idRepCab = null;
if ($almTr && $idUser) {
    $stmtProc = $db->prepare("EXEC sp_GetOrInsertRepCab ?, ?");
    $stmtProc->execute([$almTr->cod_almacen, $idUser]);

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

// Obtener filtros
$codbar = $_GET['codbar'] ?? '';
$referencia = $_GET['referencia'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$arbol_nivel1_f = $_GET['arbol_nivel1'] ?? '';
$arbol_nivel2_f = $_GET['arbol_nivel2'] ?? '';
$arbol_nivel3_f = $_GET['arbol_nivel3'] ?? '';
$marca_f = $_GET['marca'] ?? '';
$clasificacion_f = $_GET['clasificacionabc'] ?? '';

// Construir consulta dinámica
$where = [];
$params = [];

$where[] = "[WhsCode]=?";
$params[] = $almacen->cod_almacen;

if ($codbar !== '') {
    $where[] = "[CodeBars] LIKE ?";
    $params[] = "%$codbar%";
}
if ($referencia !== '') {
    $where[] = "[ItemCode] LIKE ?";
    $params[] = "%$referencia%";
}
if ($nombre !== '') {
    $where[] = "[ItemName] LIKE ?";
    $params[] = "%$nombre%";
}
if ($arbol_nivel1_f !== '') {
    $where[] = "[arbol_nivel1] = ?";
    $params[] = $arbol_nivel1_f;
}
if ($arbol_nivel2_f !== '') {
    $where[] = "[arbol_nivel2] = ?";
    $params[] = $arbol_nivel2_f;
}
if ($arbol_nivel3_f !== '') {
    $where[] = "[arbol_nivel3] = ?";
    $params[] = $arbol_nivel3_f;
}
if ($marca_f !== '') {
    $where[] = "[marca] = ?";
    $params[] = $marca_f;
}
if ($clasificacion_f !== '') {
    $where[] = "[ClasificacionABC] = ?";
    $params[] = $clasificacion_f;
}

$sql = "SELECT TOP 1000 
    CodeBars,
    ItemCode,
    ItemName,
    1 AS embalaje,
    OnHand,
    total_Transitoria_Tienda,
    total_Bodega,
    VentaUltima,
    sugerido_final AS Sugerido,
    arbol_nivel1,
    arbol_nivel2,
    arbol_nivel3,
    marca,
    ClasificacionABC
    FROM [MODULOS_SC].[reposicion].[ProcesadosCache]
    WHERE " . implode(' AND ', $where) . " 
    ORDER BY Sugerido DESC";

$stmt = $dbdev->prepare($sql);
$stmt->execute($params);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);

// Consulta solicitados
$solicitados = [];
$comentarios = [];
if ($idRepCab) {
    $stmtSol = $db->prepare("SELECT TOP 1000 [ItemCode], [Quantity], [comment] FROM [STORECONTROL].[dbo].[rep_det] WHERE [fk_id_cab]=?");
    $stmtSol->execute([$idRepCab]);
    foreach ($stmtSol->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $solicitados[$row['ItemCode']] = $row['Quantity'];
        $comentarios[$row['ItemCode']] = $row['comment'];
    }
}

?>




<div class="content">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Informacion</strong>
            </div>  
            <div class="card-body">
                <form method="GET" action="" id="form-filtros">
                    <div class="form-row">
                    <?php echo 'Solicitado de '.$almTr->cod_almacen ?> 
                    

                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Top Articulos</strong>
            </div>
            <div class="card-body">
                <table id="data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Codido Barra</th>
                            <th>Cod Producto</th>
                            <th>Descripcion</th>
                            <th>Embalaje</th>
                            <th>Stock</th>
                            <th>Transito</th>
                            <th>Stock Bodega</th>
                            <th>Venta Ult 30 dias</th>
                            <th>Sugerido</th>
                            <th>Solicitado</th>
                            <th>Dias de Inv.</th>
                            <th>Observaciones</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen as $r): ?>
                            <?php
                                $valorSolicitado = isset($solicitados[$r->ItemCode]) ? $solicitados[$r->ItemCode] : 0;
                                $comentario = isset($comentarios[$r->ItemCode]) ? $comentarios[$r->ItemCode] : '';
                                $transito = floatval($r->total_Transitoria_Tienda);
                                $onhand = floatval($r->OnHand);
                                $ventas = floatval($r->VentaUltima);
                                $solicitado = floatval($valorSolicitado);
                                // Días de inventario al cargar
                                if ($ventas > 0) {
                                    if ($solicitado == 0) {
                                        $diasInv = round((($solicitado + $transito + $onhand) / $ventas) * 30);
                                    } else {
                                        $diasInv = round((($transito + $onhand + $solicitado) / $ventas) * 30);
                                    }
                                } else {
                                    $diasInv = 0;
                                }

                            ?>
                            <tr>
                                <td><?= $r->CodeBars ?></td>
                                <td><?= $r->ItemCode ?></td>
                                <td><?= $r->ItemName ?></td>
                                <td><?= number_format($r->embalaje,0) ?></td>
                                <td><?= number_format($r->OnHand,0) ?></td>
                                <td><?= number_format($r->total_Transitoria_Tienda,0) ?></td>
                                <td><?= number_format($r->total_Bodega,0) ?></td>
                                <td><?= number_format($r->VentaUltima,0) ?></td>
                                <td><?= number_format($r->Sugerido,0) ?></td>
                                <td>
                                    <input type="number" 
                                           name="solicitar[<?= $r->ItemCode ?>]" 
                                           value="<?= $valorSolicitado ?>" 
                                           min="0" 
                                           max="<?= $r->Sugerido ?>" 
                                           data-sugerido="<?= $r->Sugerido ?>" 
                                           data-original="<?= $valorSolicitado ?>"
                                           data-transito="<?= $transito ?>"
                                           data-onhand="<?= $onhand ?>"
                                           data-ventas="<?= $ventas ?>"
                                           class="form-control form-control-sm">
                                </td>
                                <!-- dias de inventario -->
                                <td class="dias-inv"><?= number_format($diasInv, 2) ?></td>
                                <!-- observaciones -->
                                 
                                <td>
                                    <input type="text"
                                           name="comment[<?= $r->ItemCode ?>]"
                                           value="<?= htmlspecialchars($comentario) ?>"
                                           maxlength="50"
                                           class="form-control form-control-sm comment-input">
                                </td>
                                <!-- accion -->
                                <td>0</td>
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
    // Añadir fila de filtros al thead
    // ---------------------------
    $('#data-table thead tr').clone(true).appendTo('#data-table thead');
    $('#data-table thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        // Quitar filtro en columnas: Solicitado (9), Dias de Inv. (10), Observaciones (11), Accion (12)
        if (i === 9 || i === 10 || i === 11 || i === 12) {
            $(this).html('');
        } else {
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar '+title+'" />');
        }
    });

    // ---------------------------
    // Inicializar DataTable
    // ---------------------------
    var table = $('#data-table').DataTable({
        pageLength: 25,
        lengthMenu: [25,50,100],
        order: [], // quitar orden inicial
        columnDefs: [
            {
                targets: 12, // columna Solicitar
                orderable: false // quitar orden
            }
        ]
    });

    // ---------------------------
    // Aplicar filtros por columna
    // ---------------------------
    table.columns().every(function(i) {
        $('input', this.header()).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });

    // ---------------------------
    // Guardar valor original al enfocar
    // ---------------------------
    $('#data-table tbody').on('focus', 'input[type="number"][name^="solicitar"]', function() {
        $(this).data('original', this.value);
    });

    // ---------------------------
    // Validar al salir del input
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

});
</script>



<?php include_once "footer.php"; ?>
