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

$whsTr = $_SESSION["whsTr"] ?? null;
$almt = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$almt->execute([$whsTr]);
$almTr = $almt->fetch(PDO::FETCH_OBJ);

// -----------------------------
// Filtro de fechas
// -----------------------------
$fechaDesde = $_GET['fecha_desde'] ?? date('Y-m-d', strtotime('-9 days'));
$fechaHasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

// Consultar solicitudes del usuario con filtro de fechas
$sql = "SELECT c.*, u.username 
        FROM rep_cab c 
        JOIN users u ON c.idUser = u.id
        WHERE idUser = ? AND fecCreacion BETWEEN ? AND ?";
$stmt = $db->prepare($sql);
$stmt->execute([$_SESSION["idU"], $fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="content">
    <div class="col-md-6 offset-md-1 mb-3">
        <div class="card">
            <div class="card-header"><strong>Filtrar por fechas</strong></div>
            <div class="card-body">
                <form method="GET" action="" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="fecha_desde" class="mr-1">Desde:</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="<?= $fechaDesde ?>">
                    </div>
                    <div class="form-group mr-2">
                        <label for="fecha_hasta" class="mr-1">Hasta:</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="<?= $fechaHasta ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="?" class="btn btn-secondary ml-1">Restablecer</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><strong>Solicitudes</strong></div>
            <div class="card-body">
                <table id="data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha Creacion</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Usuario Solicita</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen as $r): ?>
                        <tr>
                            <td><?= date("d-m-Y H:i", strtotime($r->fecCreacion)) ?></td>
                            <td><?= $r->FromWhs ?></td>
                            <td><?= $r->ToWhs ?></td>
                            <td><?= $r->username ?></td>
                            <td><?= $r->integrado == 1 ? '🟢 Integrado a SAP' : 'Por enviar' ?></td>
                            <td>
                                <button class="btn btn-info btn-sm btn-detalle" data-idcab="<?= $r->id ?>">Detalle</button>
                                <button class="btn btn-success btn-sm btn-descargar" data-idcab="<?= $r->id ?>">PDF</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($resumen)): ?>
                        <tr><td colspan="6" class="text-center">No hay datos</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jsPDF UMD -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
$(document).ready(function() {
    $('#data-table').DataTable();

    // Detalle con SweetAlert
    $(document).on('click', '.btn-detalle', function() {
        var idCab = $(this).data('idcab');
        $.ajax({
            url: 'ajax_detalle_solicitud.php',
            type: 'POST',
            data: { idCab: idCab },
            dataType: 'json',
            success: function(resp) {
                let html = '<table class="table table-bordered table-sm"><tr><th>ItemCode</th><th>Cantidad</th></tr>';
                if(resp && resp.length > 0){
                    resp.forEach(d => {
                        html += `<tr><td>${d.ItemCode}</td><td>${d.Quantity}</td></tr>`;
                    });
                } else {
                    html += '<tr><td colspan="2">No hay detalles</td></tr>';
                }
                html += '</table>';
                Swal.fire({ title: 'Detalle', html: html, icon: 'info' });
            },
            error: function() {
                Swal.fire({ title: 'Error', text: 'No se pudo consultar los detalles.', icon: 'error' });
            }
        });
    });

    // Descargar PDF por línea
    $(document).on('click', '.btn-descargar', async function() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF("p", "mm", "a4");
        const fechaHora = "<?= date('d-m-Y H:i:s'); ?>";
        const usuario = "<?= $_SESSION['idU'].' - '.$_SESSION['username']; ?>";

        pdf.setFontSize(10);
        pdf.text("Fecha de descarga: " + fechaHora, 10, 10);
        pdf.text("Usuario: " + usuario, 10, 15);

        let startY = 25;

        const tr = $(this).closest('tr')[0];
        const cells = tr.querySelectorAll("td");
        const idCab = $(this).data('idcab');

        pdf.setFontSize(12);
        pdf.text(`Solicitud #${idCab}`, 10, startY);
        startY += 5;

        pdf.setFontSize(10);
        pdf.text(`Fecha Creación: ${cells[0].innerText}`, 10, startY);
        pdf.text(`Origen: ${cells[1].innerText}`, 60, startY);
        pdf.text(`Destino: ${cells[2].innerText}`, 120, startY);
        pdf.text(`Usuario: ${cells[3].innerText}`, 10, startY + 5);
        pdf.text(`Estado: ${cells[4].innerText}`, 60, startY + 5);
        startY += 12;

        let detalle = await fetch('ajax_detalle_solicitud.php', {
            method: 'POST',
            body: new URLSearchParams({ idCab: idCab })
        }).then(res => res.json()).catch(() => []);

        if(detalle && detalle.length > 0){
            pdf.text("Detalle:", 10, startY);
            startY += 5;
            detalle.forEach(d => {
                pdf.text(`- ${d.ItemCode}: ${d.Quantity}`, 15, startY);
                startY += 5;
                if(startY > 270){
                    pdf.addPage();
                    startY = 20;
                }
            });
        } else {
            pdf.text("No hay detalles disponibles.", 10, startY);
        }

        pdf.save(`solicitud_${idCab}.pdf`);
    });
});
</script>

<?php include_once "footer.php"; ?>
