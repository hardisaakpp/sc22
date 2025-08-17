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
$sql = "   SELECT TOP 1000 
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
        ORDER BY Sugerido DESC
        ";

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
                <strong class="card-title">Top 1000 Articulos</strong>
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
                                           value="0" 
                                           min="0" 
                                           max="<?= $r->Sugerido ?>" 
                                           data-sugerido="<?= $r->Sugerido ?>" 
                                           data-original="0"
                                           class="form-control form-control-sm">
                                </td>
                                <!-- dias de inventario -->
                                <td>0</td>
                                <!-- onbseraciones -->
                                <td>0</td>
                                <!-- accion -->
                                <td><button>👀</button></td>
                                
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

    <!-- Modal Ver Solicitados -->
    <div class="modal fade" id="modalSolicitados" tabindex="-1" role="dialog" aria-labelledby="modalSolicitadosLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSolicitadosLabel">Artículos Solicitados</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="loaderSolicitados" style="display:none;text-align:center;">
                <span class="spinner-border spinner-border-sm"></span> Cargando...
            </div>
            <div id="tablaSolicitados"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap 4 JS (para modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

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
     
    

        // ---------------------------
        // Botón Limpiar filtros
        // ---------------------------
        $('#btnLimpiar').click(function() {
            $('#form-filtros')[0].reset();
            table.search('').columns().search('').draw(); // limpiar filtros y mantener paginado
        });

    });

    $('#data-table tbody').on('blur', 'input[type="number"][name^="solicitar"]', function() {
        const sugerido = $(this).data('sugerido');
        const valorIngresado = $(this).val();

        alert("Ingresaste: " + valorIngresado + "\nSugerido: " + sugerido);
    });
    </script>

<?php include_once "footer.php"; ?>
