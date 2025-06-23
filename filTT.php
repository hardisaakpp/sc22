
<?php
    include_once "header.php";
    //si no es admin no abre

    if (!isset($_GET["idcab"])) {
        exit();
    }
    $idcab = $_GET["idcab"];
   
    $s1 = $db->query(" 
         select 
            ar.nombreGrupo, count(d.id) as items 
        from StockCab c
            join Almacen a on c.FK_ID_almacen=a.id
            left join StockDet d on c.id=d.FK_id_StockCab 
            left join Articulo ar on d.FK_ID_articulo=ar.id
        where (tipo='TT' or tipo='TP') and c.id=".$idcab."
        group by ar.nombreGrupo
        order by 1
    ");
    $scans = $s1->fetchAll(PDO::FETCH_OBJ);    
?>

<!-- Breadcrumbs-->
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
            </div>
        </div>
    </div>
<!-- /.breadcrumbs-->

<div class="content">
    <!---------------------------------------------->
    <!----------------- Content -------------------->
    <!---------------------------------------------->

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title"> POR NOMBRE DE GRUPO </strong>
            </div>
            <div class="card-body">
                <!-- Botón para seleccionar todos -->
                <button class="btn btn-primary mb-3" id="selectAllBtn">Seleccionar todos</button>
                <!-- Botón para eliminar seleccionados -->
                <button class="btn btn-danger mb-3" onclick="deleteSelected(<?php echo $idcab ?>)">🗑️ Eliminar seleccionados</button>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Grupo</th>
                            <th>Items</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($scans as $user): ?>
                            <tr>
                                <td><input type="checkbox" class="select-item" name="<?php echo $user->nombreGrupo ?>" value="<?php echo $user->nombreGrupo ?>"></td>
                                <td><?php echo $user->nombreGrupo ?></td>
                                <td><?php echo $user->items ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning delete" 
                                        onclick="delete_user($(this),'<?php echo $user->nombreGrupo ?>',<?php echo $idcab ?>)">
                                        ✖️ Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-secondary btn-lg" onclick="window.location.href='loadTT.php'">
                <i class="fa fa-sign-out"></i>&nbsp; REGRESAR
                </button>
            </div>
        </div>
    </div>

    <!---------------------------------------------->
    <!--------------Fin Content -------------------->
    <!---------------------------------------------->
</div>

<script>
    // Seleccionar todos
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.select-item');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Botón para seleccionar todos
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.select-item');
        checkboxes.forEach(cb => cb.checked = true);
    });

    // Eliminar seleccionados
    function deleteSelected(idcab) {
        const selectedCheckboxes = Array.from(document.querySelectorAll('.select-item:checked'));

        if (selectedCheckboxes.length === 0) {
            alert("No hay elementos seleccionados.");
            return;
        }

        const selectedNames = selectedCheckboxes.map(cb => cb.getAttribute('name'));
        console.log("Names seleccionados:", selectedNames);

        Swal.fire({
            title: '¿Seguro que deseas eliminar los grupos seleccionados?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedCheckboxes.forEach(cb => {
                    const name = cb.getAttribute('name');
                    const row = cb.closest('tr');
                    console.log("Ejecutando delete_user para:", name);
                    delete_user($(row).find('button.delete'), name, idcab);
                });

                Swal.fire({
                    title: 'Eliminados',
                    text: `Se eliminaron los siguientes grupos: ${selectedNames.join(', ')}`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function delete_user(row, id, idcab) {
        console.log(id);
        console.log(idcab);
        delTD(id, row, idcab);
    }

    function delTD(id, row, idcab) {
        var parametros = {
            "id": id,
            "idcab": idcab
        };

        $.ajax({
            data: parametros,
            url: 'php/deleteTFTgroups.php',
            type: 'GET',
            async: false,
            success: function(data) {
                row.closest('tr').remove();
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Se eliminó 1 registro',
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function() {
                console.log('Error de conexión - revisa tu red');
            }
        });
    }
</script>

<?php   
include_once "footer.php";
?>
