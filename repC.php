<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";
include_once "php/bd_desarrollo.php";

// Obtener nombre del almacén
$whsCica = $_SESSION["whsCica"] ?? null;
$alm = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$alm->execute([$whsCica]);
$almacen = $alm->fetch(PDO::FETCH_OBJ);

// Consulta resumen
$sql = "SELECT TOP 1000 
            [ItemCode],[ItemName],[OnHand],[MinStock],[MaxStock],
            [StockValue],[U_LEAD],[U_PER_REP],[U_STK_SEG],
            [PrecioPromedio],[IsCommited],[StockAnterior],
            CAST([PrecioPromedio] AS INT) AS Sugerido
        FROM [TEMPORALES].[transicion].[tbl_modelo_abastecimiento_tiendas_desarrollo]
        WHERE [WhsCode]=?
        ORDER BY Sugerido DESC";

$stmt = $dbdev->prepare($sql);
$stmt->execute([$almacen->cod_almacen]);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
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
                <strong class="card-title">Resumen de Depósitos</strong>
            </div>
            <div class="card-body">
                <table id="data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ItemCode</th>
                            <th>ItemName</th>
                            <th>Stock</th>
                            <th>MinStock</th>
                            <th>MaxStock</th>
                            <th>StockValue</th>
                            <th>U_LEAD</th>
                            <th>U_PER_REP</th>
                            <th>U_STK_SEG</th>
                            <th>PrecioPromedio</th>
                            <th>IsCommited</th>
                            <th>Sugerido</th>
                            <th>Solicitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen as $r): ?>
                            <tr>
                                <td><?= $r->ItemCode ?></td>
                                <td><?= $r->ItemName ?></td>
                                <td><?= number_format($r->OnHand,0) ?></td>
                                <td><?= number_format($r->MinStock,0) ?></td>
                                <td><?= number_format($r->MaxStock,0) ?></td>
                                <td><?= number_format($r->StockValue,2) ?></td>
                                <td><?= $r->U_LEAD ?></td>
                                <td><?= $r->U_PER_REP ?></td>
                                <td><?= $r->U_STK_SEG ?></td>
                                <td><?= number_format($r->PrecioPromedio,2) ?></td>
                                <td><?= number_format($r->IsCommited,2) ?></td>
                                <td><?= number_format($r->Sugerido,0) ?></td>
                                <td>
                                    <input type="number" 
                                           name="solicitar[<?= $r->ItemCode ?>]" 
                                           value="0" 
                                           min="0" 
                                           max="<?= $r->Sugerido ?>" 
                                           data-sugerido="<?= $r->Sugerido ?>" 
                                           data-original="0"
                                           class="form-control form-control-sm">
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
                    // Mantener el valor ingresado
                    $(this).data('original', value);
                } else {
                    // Volver al valor sugerido
                    this.value = sugerido;
                    $(this).data('original', sugerido);
                }
            });
        } else {
            // Valor dentro de rango, actualizar valor original
            $(this).data('original', value);
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
