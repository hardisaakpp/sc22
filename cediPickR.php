
<?php
include_once "header.php";

if (!isset($_GET["idcab"])) {
    exit();
}
$idcab = $_GET["idcab"];

$s1 = $db->query("EXEC sp_getGrpSotConBinCode $idcab ");
$scans = $s1->fetchAll(PDO::FETCH_OBJ);
?>

<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    .col-barcodes {
        font-size: 11px;
    } /* o el tamaño que prefieras */
    .col-nombre {
        font-size: 10px;
    } /* o el tamaño que prefieras */
    .fila-completa {
        background-color: #d4edda !important; /* verde claro */
    }
   .fila-parcial {
        background-color: #e2e3e5 !important; /* gris claro */
    }

</style>

</content">

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">PICKING RECOLECCION TOMA N°<?php echo $idcab ?></strong>  
            </div>
            <form id="form-codigo">
                <div class="input-group mb-3 flex-nowrap">
                <input type="text" class="form-control" id="UBICACION" placeholder="Escanea UBICACION" >    
                <input type="text" class="form-control" id="codigo" placeholder="Escanea o escribe el código" autofocus>
                    <div class="input-group-append dropdown">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </div>
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            ⚙️
                        </button>
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu2">
                            <!--<button type="button" class="dropdown-item" onclick="window.location.href='TTscanDel.php?idcab=<?= $idcab ?>'">ELIMINAR ITEMS</button>
                            <button class="dropdown-item" onclick="window.location.href='TTscanRes.php?idcab=<?= $idcab ?>'">RESUMEN</button>-->
                            <button type="button" class="dropdown-item" onclick="window.location.href='wllcm.php'">X</button>
                        </div>
                    </div>
                </div>
            </form>

            <table id="tabla">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Solcitado</th>
                        <th>Ubicacion sugerida</th>
                        <th>Escaneados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scans as $item): ?>
                        <tr data-codigo="<?= $item->codigoBarras ?>">
                            <td class="col-barcodes"><?= $item->codigoBarras ?></td>
                            <td class="col-nombre"><?= $item->descripcion ?></td>
                            
                            <td><?= $item->stock ?></td>
                            <td><?= $item->BinCode ?></td>
                            <td class="escaneados">0</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
 document.getElementById('form-codigo').addEventListener('submit', function(e) {
    e.preventDefault();
    const codigoIngresado = document.getElementById('codigo').value.trim();
    if (!codigoIngresado) return;

    const tabla = document.querySelector('#tabla tbody');
    let fila = document.querySelector(`tr[data-codigo="${codigoIngresado}"]`);

    if (fila) {
        const escaneadosCelda = fila.querySelector('.escaneados');
        const cantidadActual = parseInt(escaneadosCelda.textContent) || 0;
        const stockCelda = fila.children[2];
        const stock = parseInt(stockCelda.textContent) || 0;

        if (cantidadActual >= stock) {
            alert("Este producto ya ha sido completado. No se puede escanear más.");
            return;
        }

        const nuevaCantidad = cantidadActual + 1;
        escaneadosCelda.textContent = nuevaCantidad;

        // Actualizar color de fila según cantidad
        fila.classList.remove('fila-completa', 'fila-parcial');
        if (nuevaCantidad === stock) {
            fila.classList.add('fila-completa');
        } else if (nuevaCantidad > 0 && nuevaCantidad < stock) {
            fila.classList.add('fila-parcial');
        }

        // Mover la fila al inicio de la tabla
        tabla.prepend(fila);
    } else {
        alert("El código ingresado no existe en la lista. Verifica e intenta nuevamente.");
    }

    document.getElementById('codigo').value = '';
    document.getElementById('codigo').focus();
});


</script>

<?php include_once "footer.php"; ?>
