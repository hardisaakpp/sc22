<?php
include_once "header.php";

// Control de acceso
if ($userAdmin != 1 && $userAdmin != 5) {
    echo ('<h4> NO TIENE ACCESO</h4>');
} else {
    // Fechas por defecto
    $desde = date('Y-m-d', strtotime('-2 days'));
    $hasta = date('Y-m-d');
?>

<div class="content">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Crear Lista de Distribución</strong>
                </div>
                <div class="card-body">
                    <form id="form-fechas" class="form-inline mb-3">
                        <label for="desde">Solicitudes desde:</label>
                        <input type="date" name="desde" id="desde" class="form-control mx-2" value="<?php echo $desde ?>" required>
                        <label for="hasta">hasta:</label>
                        <input type="date" name="hasta" id="hasta" class="form-control mx-2" value="<?php echo $hasta ?>" required>
                        <button type="submit" class="btn btn-primary me-2">Buscar 🔎</button>
                    </form>

                    <div id="contenedor-listado">
                        <!-- Aquí se cargará el listado dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <strong>Solicitudes Seleccionadas</strong>
                </div>
                <div class="card-body">
                    <form id="form-grupo" method="post" action="php/guardar_grupoCE.php">
                        <table class="table table-bordered" id="tabla-grupo">
                            <thead>
                                <tr>
                                    <th>SOLICITUD</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <input type="hidden" name="ids" id="ids-grupo">
                        <button type="button" id="generar-grupo" class="btn btn-success">Generar Grupo</button>
                        <button type="button" id="vaciar-lista" class="btn btn-outline-secondary">Vaciar lista</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ingresar nombre del grupo -->
<div class="modal fade" id="modal-nombre-grupo" tabindex="-1" aria-labelledby="modal-nombre-grupo-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-nombre-grupo-label">Nombre del Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="nombre-grupo" class="form-control" placeholder="Ingrese nombre del grupo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="guardar-grupo" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery y AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let seleccionados = new Set(); // IDs seleccionados

function cargarListado() {
    const desde = $('#desde').val();
    const hasta = $('#hasta').val();

    $.get('php/listado_solicitudesCE.php', { desde, hasta }, function(data) {
        $('#contenedor-listado').html(data);

        // Inicializar DataTable with column filters
        const tabla = $('#tabla-solicitudes').DataTable({
            destroy: true, // important for reinitialization
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var th = $(column.header());
                    var title = th.text();
                    th.html(`<div style="display: flex; flex-direction: column;">
                                <span style="font-weight: bold;">${title}</span>
                                <input type="text" placeholder="Filtrar" style="width: 100%; font-size: 12px; margin-top: 4px;" />
                            </div>`);
                    $('input', th).on('keyup change', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });
                });
            },
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        // Restore checks
        seleccionados.forEach(id => {
            $(`.seleccionar[data-id="${id}"]`).prop('checked', true);
        });
    });
}

$(document).on('change', '.seleccionar', function () {
    const id = $(this).data('id');
    const tablaGrupo = $('#tabla-grupo tbody');

    if (this.checked) {
        if (!seleccionados.has(id)) {
            seleccionados.add(id);
            const nuevaFila = `<tr data-id="${id}">
                <td>${id}</td>
                <td><button type="button" class="btn btn-danger btn-sm quitar">Quitar</button></td>
            </tr>`;
            tablaGrupo.append(nuevaFila);
        }
    } else {
        seleccionados.delete(id);
        tablaGrupo.find(`tr[data-id="${id}"]`).remove();
    }

    actualizarInputGrupo();
});

$(document).on('click', '.quitar', function () {
    const fila = $(this).closest('tr');
    const id = fila.data('id');
    fila.remove();
    seleccionados.delete(id);
    $(`.seleccionar[data-id="${id}"]`).prop('checked', false);
    actualizarInputGrupo();
});

function actualizarInputGrupo() {
    const ids = [];
    $('#tabla-grupo tbody tr').each(function () {
        ids.push($(this).data('id'));
    });
    $('#ids-grupo').val(ids.join(','));
}

$(document).on('change', '#select-all', function () {
    $('.seleccionar').prop('checked', this.checked).trigger('change');
});


$(document).on('click', '#vaciar-lista', function () {
    seleccionados.clear();
    $('#tabla-grupo tbody').empty();
    $('.seleccionar').prop('checked', false).trigger('change');
});

$(document).on('click', '#generar-grupo', function () {
    if (seleccionados.size === 0) {
        alert('Elija items');
    } else {
        $('#modal-nombre-grupo').modal('show');
    }
});

$(document).on('click', '#guardar-grupo', function () {
    const nombreGrupo = $('#nombre-grupo').val();
    if (nombreGrupo.trim() === '') {
        alert('Ingrese un nombre para el grupo');
    } else {
        // Agregar el parámetro idU de sesión al formulario antes de enviar
        $('#form-grupo').append(`<input type="hidden" name="idU" value="<?= $_SESSION['idU'] ?>">`);
        $('#form-grupo').append(`<input type="hidden" name="nombre_grupo" value="${nombreGrupo}">`);
        $('#form-grupo').submit();
    }
});

$('#form-fechas').on('submit', function(e) {
    e.preventDefault();
    cargarListado();
});

$(document).ready(function() {
    // Ejecutar el SP antes de cargar el listado
    $.get('php/ejecutar_sp.php?sp=sp_sot_mergeCE', function() {
        cargarListado(); // Carga inicial después de ejecutar el SP
    });
});
</script>

<?php
}
include_once "footer.php";
?>
