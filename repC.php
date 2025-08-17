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

// Consulta resumen
$sql = "SELECT TOP 1000 
		  CodeBars,	
		  ItemCode,	
		  ItemName,	
		  1 AS embalaje,	
		  OnHand,	
		  total_Transitoria_Tienda,	
		  total_Bodega,	
		  VentaUltima,	
		  sugerido_final AS Sugerido
        FROM [MODULOS_SC].[reposicion].[ProcesadosCache]
        WHERE [WhsCode]=?
        ORDER BY Sugerido DESC";

$stmt = $dbdev->prepare($sql);
$stmt->execute([$almacen->cod_almacen]);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);

// Consulta solicitados
$solicitados = [];
if ($idRepCab) {
    $stmtSol = $db->prepare("SELECT TOP 1000 [ItemCode], [Quantity] FROM [STORECONTROL].[dbo].[rep_det] WHERE [fk_id_cab]=?");
    $stmtSol->execute([$idRepCab]);
    foreach ($stmtSol->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $solicitados[$row['ItemCode']] = $row['Quantity'];
    }
}
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
                        <!-- Grupo -->
                        <div class="form-group col-md-2">
                            <label for="grupo">Grupo</label>
                            <select name="grupo" id="grupo" class="form-control">
                                <option value="TODOS">TODOS</option>
                                <option value="MEDIAS">MEDIAS</option>
                                <option value="PIJAMAS">PIJAMAS</option>
                                <option value="BOXER">BOXER</option>
                                <!-- agregar más opciones según necesidad -->
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="form-group col-md-2">
                            <label>Estado</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estado" id="estadoTodos" value="TODOS" checked>
                                <label class="form-check-label" for="estadoTodos">TODOS</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estado" id="estadoActivos" value="ACTIVOS">
                                <label class="form-check-label" for="estadoActivos">ACTIVOS</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estado" id="estadoInactivos" value="INACTIVOS">
                                <label class="form-check-label" for="estadoInactivos">INACTIVOS</label>
                            </div>
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
                                           class="form-control form-control-sm">
                                </td>
                                                                <!-- dias de inventario -->
                                <td>0</td>
                                <!-- onbseraciones -->
                                <td>0</td>
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
        if(title !== 'Solicitar') { // no poner filtro en "Solicitar"
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar '+title+'" />');
        } else {
            $(this).html(''); // dejar vacío
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

        if (isNaN(value) || value < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Valor inválido',
                text: 'El valor no puede ser menor que 0',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                this.value = original;
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
                } else {
                    this.value = sugerido;
                    $(this).data('original', sugerido);
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
        }
    });

    // ---------------------------
    // Botón Limpiar filtros
    // ---------------------------
    $('#btnLimpiar').click(function() {
        $('#form-filtros')[0].reset();
        table.columns().search('').draw(); // limpiar filtros de DataTable
    });

});
</script>



<?php include_once "footer.php"; ?>
