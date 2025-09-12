<?php
include_once "header.php";
?>

<div class="container mt-4">
    <form class="form-inline" method="GET">
        <h3 style="color:gray;">Consulta Orden de Compra:</h3>
        <input type="text" class="form-control mb-2 mr-sm-2" 
               id="oc" name="oc" placeholder="Nro OC" value="" required>
        <input type="submit" name="find" id="find" value="Buscar 🔎" class="btn btn-primary">
    </form>
</div>

<div class="container mt-4">
<?php
if (isset($_GET['oc'])) {
    $oc = $_GET['oc'];

    // Ejecutar SP que llena la tabla ODC_sap y devuelve los registros
    $sentencia = $db->query("EXEC sp_SAP_OC_Todas '".$oc."' ");
    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if (count($rows) == 0) {
        echo "<h4 style='color:red;'>No se encontraron registros para la OC ".$oc."</h4>";
    } else {
        $fecha = $rows[0]->FechaHoraActualizacion ?? "";
        $estadoOC = $rows[0]->DocStatus ?? 'O';

        // Determinar color y texto
        if ($estadoOC == 'O') {
            $estadoColor = '#FF6347';
            $estadoTexto = 'Pendiente';
        } else if ($estadoOC == 'C') {
            $estadoColor = '#28a745';
            $estadoTexto = 'Cerrado 🔒';
        } else {
            $estadoColor = 'gray';
            $estadoTexto = $estadoOC;
        }

        // Card resumen con flex para botones
        echo "
        <div class='card shadow-sm mb-3'>
            <div class='card-body d-flex flex-column flex-md-row justify-content-between align-items-start'>
                <div>
                    <h5 class='card-title'>Orden de Compra Nº $oc</h5>
                    <p class='card-text'>Fecha de creación en sistema: <b>$fecha</b></p>
                    <p class='card-text'>Estado: 
                        <span style='background-color:$estadoColor; color:white; padding:5px 10px; border-radius:5px;'>
                            <b>$estadoTexto</b>
                        </span>
                    </p>
                </div>
                <div class='mt-2 mt-md-0 d-flex flex-column flex-sm-row gap-2'>
                    <form method='post' action='download_oc.php'>
                        <input type='hidden' name='oc' value='$oc'>
                        <button type='submit' class='btn btn-success'>⬇️ Descargar Excel</button>
                    </form>";

        if ($estadoOC === 'O') {
            echo "
                    <button type='button' class='btn btn-primary' onclick=\"document.getElementById('modalAceptacion').style.display='block'\">Aceptar Ingreso de Mercadería</button>";
        }

        echo "
                </div>
            </div>
        </div>";

        // Modal de aceptación
        echo "
        <div id='modalAceptacion' class='modal' style='display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);'>
            <div class='modal-dialog' style='margin:10% auto; max-width:400px;'>
                <div class='modal-content p-3'>
                    <h5>Confirmar Ingreso</h5>
                    <form method='post' action='aceptarIngreso.php'>
                        <input type='hidden' name='DocNum' value='$oc'>
                        <div class='form-group'>
                            <label>Responsable que acepta el Ingreso:</label>
                            <input type='text' name='Responsable' class='form-control' required>
                        </div>
                        <div class='d-flex justify-content-end gap-2 mt-3'>
                            <button type='button' class='btn btn-secondary' onclick=\"document.getElementById('modalAceptacion').style.display='none'\">Cancelar</button>
                            <button type='submit' class='btn btn-primary'>Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>";

        // Tabla de detalle
        echo "<div class='table-responsive'>
              <table class='table table-bordered'>
              <thead class='thead-dark'>
                <tr>
                    <th>DocNum</th>
                    <th>LineNum</th>
                    <th>ItemCode</th>
                    <th>Quantity</th>
                    <th>WhsCode</th>
                    <th>BaseType</th>
                    <th>BaseEntry</th>
                    <th>BaseLine</th>
                </tr>
              </thead>
              <tbody>";

        foreach ($rows as $r) {
            $baseType  = 22;
            $baseEntry = $r->DocEntry;
            $baseLine  = $r->LineNum;

            echo "<tr>
                <td>{$r->DocNum}</td>
                <td>{$r->LineNum}</td>
                <td>{$r->ItemCode}</td>
                <td>{$r->Quantity}</td>
                <td>{$r->WhsCode}</td>
                <td>{$baseType}</td>
                <td>{$baseEntry}</td>
                <td>{$baseLine}</td>
            </tr>";
        }

        echo "</tbody></table></div>";
    }
}
?>
</div>

<?php include_once "footer.php"; ?>
