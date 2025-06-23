<?php
include_once "header.php";

if (!isset($_GET["idcab"])) {
    exit();
}
$idcab = $_GET["idcab"];

$s1 = $db->query("EXEC sp_getCodBarEnabled '$idcab';");
$lsArts = $s1->fetchAll(PDO::FETCH_OBJ);

// Extraer solo los códigos de barra
$barcodes = array_map(fn($item) => $item->codigoBarras, $lsArts);

$s1 = $db->query("exec sp_getTFT_resumen ".$idcab.",3");
$scans = $s1->fetchAll(PDO::FETCH_OBJ);
?>

<style>

    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
            .col-barcodes {
     font-size: 11px;} /* o el tamaño que prefieras */
        .col-nombre {
     font-size: 10px;} /* o el tamaño que prefieras */
    .fila-completa {
  background-color: #d4edda !important; /* verde claro */
  

}



</style>


</content">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">RECONTEO TOMA N°<?php  echo $idcab ?></strong>  
                </div>
   <form id="form-codigo">
  <div class="input-group mb-3 flex-nowrap">
    <input type="text" class="form-control" id="codigo" placeholder="Escanea o escribe el código" autofocus>
    <div class="input-group-append dropdown">
    <div class="input-group-append">
      <button type="submit" class="btn btn-primary">Agregar</button>
    </div>


      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        ⚙️
      </button>
      <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu2">
        <button type="button" class="dropdown-item" onclick="window.location.href='TTscanDel.php?idcab=<?= $idcab ?>'">ELIMINAR ITEMS</button>
        <button class="dropdown-item" onclick="window.location.href='TTscanRes.php?idcab=<?= $idcab ?>'">RESUMEN</button>
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
                <th>Stock</th>
                <th>Escaneados</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scans as $item): ?>
                <tr data-codigo="<?= $item->codigoBarras ?>">
                    <td class="col-barcodes"><?= $item->codigoBarras ?></td>
                    <td class="col-nombre"><?= $item->descripcion ?></td>
                    <td><?= $item->stock ?></td>
                    <td class="escaneados">0</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


            </div>
        </div>
    </div>


<!-- Pasar los códigos válidos a JavaScript -->
<script>
  const codigosValidos = <?= json_encode($barcodes) ?>;
</script>

<script>
  document.getElementById('form-codigo').addEventListener('submit', function(e) {
    e.preventDefault();
    const codigoIngresado = document.getElementById('codigo').value.trim();
    if (!codigoIngresado) return;

    const esValido = codigosValidos.includes(codigoIngresado);
    if (!esValido) {
      alert('⚠️ Código no válido. No se encuentra en la lista de productos habilitados.');
      document.getElementById('codigo').value = '';
      document.getElementById('codigo').focus();
      return;
    }

    const tabla = document.querySelector('#tabla tbody');
    let fila = document.querySelector(`tr[data-codigo="${codigoIngresado}"]`);

    if (fila) {
      const escaneadosCelda = fila.querySelector('.escaneados');
      const cantidadActual = parseInt(escaneadosCelda.textContent) || 0;
      const nuevaCantidad = cantidadActual + 1;
      escaneadosCelda.textContent = nuevaCantidad;

      // Comparar con stock
      const stockCelda = fila.children[2];
      const stock = parseInt(stockCelda.textContent) || 0;

      if (nuevaCantidad === stock) {
        fila.classList.add('fila-completa');
      } else {
        fila.classList.remove('fila-completa');
      }

    } else {
      // Agregar nueva fila si es válido pero no está en la tabla
      const nuevaFila = document.createElement('tr');
      nuevaFila.setAttribute('data-codigo', codigoIngresado);
      nuevaFila.innerHTML = `
        <td>${codigoIngresado}</td>
        <td>Producto válido</td>
        <td>0</td>
        <td class="escaneados">1</td>
      `;
      tabla.appendChild(nuevaFila);
    }

    document.getElementById('codigo').value = '';
    document.getElementById('codigo').focus();
  });
</script>


<?php include_once "footer.php"; ?>
