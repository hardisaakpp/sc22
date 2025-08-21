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
    embalaje,
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
    $stmtSol = $db->prepare("declare @idcab int;
set @idcab=?;

 select isnull(q1.ItemCode ,q2.ItemCode) as ItemCode, 
		isnull(q1.Quantity,0) as Quantity,
		isnull(q2.Quantity,0) as solicitados,
		q1.comment
 from 
 (
  SELECT d.[ItemCode], d.[Quantity], d.[comment] 
  FROM [STORECONTROL].[dbo].[rep_det] d join [STORECONTROL].[dbo].rep_cab c on d.fk_id_cab=c.id
  WHERE d.[fk_id_cab]=@idcab and CAST(c.fecCreacion AS date) = CAST(GETDATE() AS date) and d.Quantity>0
 ) q1  full  join
 ( 
  SELECT d.[ItemCode], sum(d.[Quantity] ) as Quantity
  FROM [STORECONTROL].[dbo].[rep_det] d join [STORECONTROL].[dbo].rep_cab c on d.fk_id_cab=c.id
  WHERE d.[fk_id_cab]<>@idcab and CAST(c.fecCreacion AS date) = CAST(GETDATE() AS date)  and d.Quantity>0
  group by d.[ItemCode]
 )q2 on q1.[ItemCode]=q2.[ItemCode]");
    $stmtSol->execute([$idRepCab]);
    foreach ($stmtSol->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $solicitados[$row['ItemCode']] = $row['Quantity'];
        $comentarios[$row['ItemCode']] = $row['comment'];
    }
}

// Consultas para los filtros de árbol y nuevos filtros
$arbol_nivel1 = [];
$arbol_nivel2 = [];
$arbol_nivel3 = [];
$marcas = [];
$clasificaciones = [];

$stmtN1 = $dbdev->query("SELECT DISTINCT [arbol_nivel1] FROM [MODULOS_SC].[reposicion].[ProcesadosCache] WHERE [WhsCode]='".$almacen->cod_almacen."'");
$arbol_nivel1 = $stmtN1->fetchAll(PDO::FETCH_COLUMN);

$stmtN2 = $dbdev->query("SELECT DISTINCT [arbol_nivel2] FROM [MODULOS_SC].[reposicion].[ProcesadosCache] WHERE [WhsCode]='".$almacen->cod_almacen."'");
$arbol_nivel2 = $stmtN2->fetchAll(PDO::FETCH_COLUMN);

$stmtN3 = $dbdev->query("SELECT DISTINCT [arbol_nivel3] FROM [MODULOS_SC].[reposicion].[ProcesadosCache] WHERE [WhsCode]='".$almacen->cod_almacen."'");
$arbol_nivel3 = $stmtN3->fetchAll(PDO::FETCH_COLUMN);

$stmtMarca = $dbdev->query("SELECT DISTINCT [marca] FROM [MODULOS_SC].[reposicion].[ProcesadosCache] WHERE [WhsCode]='".$almacen->cod_almacen."'");
$marcas = $stmtMarca->fetchAll(PDO::FETCH_COLUMN);

$stmtABC = $dbdev->query("SELECT DISTINCT [ClasificacionABC] FROM [MODULOS_SC].[reposicion].[ProcesadosCache] WHERE [WhsCode]='".$almacen->cod_almacen."'");
$clasificaciones = $stmtABC->fetchAll(PDO::FETCH_COLUMN);
?>



<div class="content">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Filtros</strong>
            </div>  
            <div class="card-body">
                <form method="GET" action="" id="form-filtros">
                    <div class="form-row">
                        <!-- Codigo Barras -->
                        <div class="form-group col-md-2">
                            <label for="codbar">Codigo de barras</label>
                            <input type="text" name="codbar" id="codbar" class="form-control" placeholder="Codigo de Barras">
                        </div>

                        <!-- Referencia -->
                        <div class="form-group col-md-2">
                            <label for="referencia">Referencia</label>
                            <input type="text" name="referencia" id="referencia" class="form-control" placeholder="Referencia">
                        </div>

                        <!-- Nombre -->
                        <div class="form-group col-md-2">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre">
                        </div>
               
                        <!-- Filtro arbol_nivel1 -->
                        <div class="form-group col-md-2 d-none d-md-block">
                            <label for="arbol_nivel1">Unidad</label>
                            <select name="arbol_nivel1" id="arbol_nivel1" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($arbol_nivel1 as $n1): ?>
                                    <option value="<?= htmlspecialchars($n1) ?>"><?= htmlspecialchars($n1) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro arbol_nivel2 -->
                        <div class="form-group col-md-2 d-none d-md-block">
                            <label for="arbol_nivel2">Categoría</label>
                            <select name="arbol_nivel2" id="arbol_nivel2" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($arbol_nivel2 as $n2): ?>
                                    <option value="<?= htmlspecialchars($n2) ?>"><?= htmlspecialchars($n2) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro arbol_nivel3 -->
                        <div class="form-group col-md-2 d-none d-md-block">
                            <label for="arbol_nivel3">Línea</label>
                            <select name="arbol_nivel3" id="arbol_nivel3" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($arbol_nivel3 as $n3): ?>
                                    <option value="<?= htmlspecialchars($n3) ?>"><?= htmlspecialchars($n3) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro marca -->
                        <div class="form-group col-md-2 d-none d-md-block">
                            <label for="marca">Marca</label>
                            <select name="marca" id="marca" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= htmlspecialchars($marca) ?>"><?= htmlspecialchars($marca) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro ClasificacionABC -->
                        <div class="form-group col-md-2 d-none d-md-block">
                            <label for="clasificacionabc">Clasificación ABC</label>
                            <select name="clasificacionabc" id="clasificacionabc" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($clasificaciones as $abc): ?>
                                    <option value="<?= htmlspecialchars($abc) ?>"><?= htmlspecialchars($abc) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="form-row mt-2">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar filtros</button>
                        </div>
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
                            <th class="d-none d-md-table-cell">Codido Barra</th>
                            <th>Cod Producto</th>
                            <th>Descripcion</th>
                            <th class="d-none d-md-table-cell">Embalaje</th>
                            <th class="d-none d-md-table-cell">Stock</th>
                            <th class="d-none d-md-table-cell">Transito</th>
                            <th>Total Disponible</th>
                            <th>Stock Bodega</th>
                            <th class="d-none d-md-table-cell">Venta Ult 30 dias</th>
                            <th>Sugerido</th>
                            <th>Solicitado</th>
                            <th class="d-none d-md-table-cell">Dias de Inv.</th>
                            <th class="d-none d-md-table-cell">Observaciones</th>
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
            $totalDisponible = $onhand + $transito + $solicitado;
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
            <td class="d-none d-md-table-cell"><?= $r->CodeBars ?></td>
            <td><?= $r->ItemCode ?></td>
            <td><?= $r->ItemName ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->embalaje,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->OnHand,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->total_Transitoria_Tienda,0) ?></td>
            <td class="total-disponible"><?= number_format($totalDisponible,0) ?></td>
            <td><?= number_format($r->total_Bodega,0) ?></td>
            <td class="d-none d-md-table-cell"><?= number_format($r->VentaUltima,0) ?></td>
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
            <td class="d-none d-md-table-cell dias-inv"><?= number_format($diasInv, 2) ?></td>
            <td class="d-none d-md-table-cell">
                <input type="text"
                    name="comment[<?= $r->ItemCode ?>]"
                    value="<?= htmlspecialchars($comentario) ?>"
                    maxlength="50"
                    class="form-control form-control-sm comment-input">
            </td>
            <td>
                <button type="button" class="btn btn-info btn-modal-codprod" data-codprod="<?= htmlspecialchars($r->ItemCode) ?>">Ver</button>
            </td>
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
    // Validar al salir del input solicitado
    // ---------------------------
 $('#data-table tbody').on('blur input', 'input[type="number"][name^="solicitar"]', function () {
    const sugerido = parseFloat($(this).data('sugerido'));
    let value = parseFloat(this.value);
    const original = $(this).data('original');
    const itemcode = $(this).attr('name').match(/\[(.*?)\]/)[1];
    const towhs = "<?= $almTr->cod_almacen ?>";
    const idcab = "<?= $idRepCab ?>";
    const stockBodega = parseFloat($(this).closest('tr').find('td').eq(7).text().replace(/,/g, ''));

    let transito = parseFloat($(this).data('transito')) || 0;
    let onhand = parseFloat($(this).data('onhand')) || 0;
    let ventas = parseFloat($(this).data('ventas')) || 0;
    let $row = $(this).closest('tr');
    let $diasInvTd = $row.find('td.dias-inv');
    let $totalDisponibleTd = $row.find('td.total-disponible');
    let solicitado = value;
    let totalDisponible = onhand + transito + solicitado;
    let $input = $(this); // ✅ referencia al input

    // Mostrar disponible provisional mientras llega AJAX
    $totalDisponibleTd.text(totalDisponible.toFixed(0));

    $.ajax({
        url: "ajax_solicitados.php",
        type: "POST",
        data: {
            itemcode: itemcode,
            towhs: towhs
        },
        dataType: "json",
        success: function (res) {
            let solicitadostiendas = res.solicitados || 0;
            console.log("Solicitados tiendas:", solicitadostiendas);

            // ✅ Validaciones
            if (value > stockBodega - solicitadostiendas) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock insuficiente',
                    text: 'No puede solicitar más que el Stock DISPONIBLE de Bodega (' + (stockBodega - solicitadostiendas) + ')',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    $input.val(original);
                    solicitado = parseFloat(original) || 0;
                    recalcularTotales();
                });
                return;
            }

            if (isNaN(value) || value < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Valor inválido',
                    text: 'El valor no puede ser menor que 0',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    $input.val(original);
                    solicitado = parseFloat(original) || 0;
                    recalcularTotales();
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
                    $input.val(original);
                    solicitado = parseFloat(original) || 0;
                    recalcularTotales();
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
                        $input.data('original', value);
                        guardarSolicitud(value);
                        solicitado = value;
                    } else {
                        $input.val(sugerido);
                        $input.data('original', sugerido);
                        guardarSolicitud(sugerido);
                        solicitado = sugerido;
                    }
                    recalcularTotales();
                });
            } else {
                $input.data('original', value);
                guardarSolicitud(value);
                solicitado = value;
                recalcularTotales();
            }
        },
        error: function (xhr, status, err) {
            console.error("AJAX Error:", err);
        }
    });

    function recalcularTotales() {
        let diasInv = 0;
        if (ventas > 0) {
            diasInv = ((transito + onhand + solicitado) / ventas) * 30;
        }
        totalDisponible = onhand + transito + solicitado;
        $diasInvTd.text(diasInv.toFixed(2));
        $totalDisponibleTd.text(totalDisponible.toFixed(0));
    }

    function guardarSolicitud(value) {
        $input.data('original', value);
        $.ajax({
            url: 'ajax_repdet.php',
            type: 'POST',
            data: {
                idcab: idcab,
                towhs: towhs,
                itemcode: itemcode,
                quantity: value
            },
            success: function (resp) {
                console.log("Guardado:", resp);
            },
            error: function (xhr) {
                console.error("Error al guardar:", xhr.responseText);
            }
        });
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
    $('#data-table').on('click', '.btn-modal-codprod', function() {
        var codprod = $(this).data('codprod');
        var whscode = "<?= $almacen->cod_almacen ?>";
        // Obtener el valor solicitado de la fila actual
        var solicitado = $(this).closest('tr').find('input[name^="solicitar"]').val();
        // Obtener total_Bodega y total_Transitoria_Bodega de la fila actual
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


<?php include_once "footer.php"; ?>
