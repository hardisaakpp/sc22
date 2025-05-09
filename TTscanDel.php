
<?php
include_once "header.php";

if (!isset($_GET["idcab"])) {
    exit();
}
$idcab = $_GET["idcab"];

$s1 = $db->query("
select top 50 s.id, fecScan, barcode, a.ID_articulo, a.descripcion 
from StockScan s
left join Articulo a on s.barcode=a.codigoBarras
where fk_id_stockCab=".$idcab."
and id_user=".$userId."
order by fecScan desc
");
$scans = $s1->fetchAll(PDO::FETCH_OBJ);    
?>

<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>TOMA FISICA TOTAL <?php echo $idcab ?></h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8"> 
                <div class="page-header float-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Acciones
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <button type="button" class="dropdown-item" onclick="window.location.href='TTscans.php?idcab=<?php echo $idcab ?>'">ESCANEAR</button>
                            <button type="button" class="dropdown-item" onclick="window.location.href='TTscanRes.php?idcab=<?php echo $idcab ?>'">RESUMEN</button>
                            <button type="button" class="dropdown-item" onclick="window.location.href='wllcm.php'">SALIR</button>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">50 ULTIMOS CODIGOS INGRESADOS </strong>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="toggle_select_all()">Seleccionar Todos</button>
                <button class="btn btn-danger" onclick="delete_selected()">Eliminar Seleccionados</button>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Seleccionar</th>
                            <th>Fecha</th>
                            <th>Codigo Barras</th>
                            <th>Item Code</th>
                            <th>Descripcion</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php  
                    foreach($scans as $user){ ?>
                        <tr>
                            <td><input type="checkbox" class="delete-checkbox" id="<?php echo $user->id ?>"></td>
                            <td><?php echo $user->fecScan ?></td>
                            <td><?php echo $user->barcode ?></td>
                            <td><?php echo $user->ID_articulo ?></td>
                            <td><?php echo $user->descripcion ?></td>
                            <td name='op'><a class="btn btn-warning btn-sm delete" onclick="delete_user($(this),<?php echo $user->id ?>)">❌</a></td>
                        </tr>
                    <?php 
                    } 
                    ?>   
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function delete_user(row, id) { 
    delTD(id, row);
}

function delTD(id, row) {
    var parametros = {
        "id": id
    };

    $.ajax({
        data: parametros,
        url: 'php/scanDelete.php',
        type: 'GET',
        async: false,
        success: function(data){
            row.closest('tr').remove();
            console.log('Eliminado ID: ' + id); // Mostrar el ID eliminado en la consola
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Se eliminó 1 registro',
                showConfirmButton: false,
                timer: 1500
            });
        },
        error: function(){
            console.log('error de conexión - revisa tu red');
        }
    });
}

function delete_selected() {
    var selectedIds = [];
    $('.delete-checkbox').each(function() {
        var id = $(this).attr('id');
        var isChecked = $(this).is(':checked');
        console.log('ID: ' + id + ', Marcado: ' + isChecked);
        if (isChecked) {
            selectedIds.push(id);
        }
    });

    if (selectedIds.length > 0) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminarán " + selectedIds.length + " registros.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedIds.forEach(function(id) {
                    console.log('Eliminando ID: ' + id); // Mostrar el ID que se va a eliminar en la consola
                    var parametros = {
                        "id": id
                    };

                    $.ajax({
                        data: parametros,
                        url: 'php/scanDelete.php',
                        type: 'GET',
                        async: false,
                        success: function(data){
                            $('.delete-checkbox[id="' + id + '"]').closest('tr').remove();
                            console.log('Eliminado ID: ' + id); // Mostrar el ID eliminado en la consola
                        },
                        error: function(){
                            console.log('error de conexión - revisa tu red');
                        }
                    });
                });

                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Se eliminaron ' + selectedIds.length + ' registros',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    } else {
        Swal.fire({
            position: 'top-end',
            icon: 'warning',
            title: 'No se seleccionaron registros',
            showConfirmButton: false,
            timer: 1500
        });
    }
}

function toggle_select_all() {
    var allChecked = $('.delete-checkbox').length === $('.delete-checkbox:checked').length;
    $('.delete-checkbox').prop('checked', !allChecked);
}
</script>


<?php   
include_once "footer.php";
?>

