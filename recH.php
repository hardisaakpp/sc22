<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";

$whsCica = $_SESSION["whsCica"] ?? null;
$whsTr   = $_SESSION["whsTr"] ?? null;

if (empty($whsCica) || empty($whsTr) || $whsCica <= 0 || $whsTr <= 0) {
    exit("El usuario no tiene configurados los almacenes.");
}

// Buscar almacenes
$s11 = $db->query("SELECT cod_almacen FROM almacen WHERE id = '".$whsTr."' ");
$TEMPa1 = $s11->fetchObject();
$almacenTran = $TEMPa1->cod_almacen;

$s10 = $db->query("SELECT cod_almacen FROM almacen WHERE id = '".$whsCica."' ");
$TEMPa2 = $s10->fetchObject();
$almacenR = $TEMPa2->cod_almacen;

// Manejo de fecha única
$fechaHoy = date("Y-m-d");
$fecha = $_GET["fecha"] ?? $fechaHoy;

// ⚡ Ejecutar SP antes de la consulta para actualizar datos
$sp = $db->prepare("EXEC sp_ActualizarNumTransferenciaNvaPorFecha :fecha");
$sp->execute([":fecha" => $fecha]);

// Consulta resumen SOLO por un día
$sql = "
    SELECT c.Id, c.NumTransferencia, c.NumTransferenciaNva,
           CAST(SUM(d.CantidadAbierta) AS INT) AS Cantidad, 
           CAST(SUM(d.Scan) AS INT) AS Escaneados, c.FechaIntegracion, c.Responsable
    FROM [dbo].[TransferenciasCabecera] c 
    JOIN TransferenciasDetalle d ON c.Id = d.id_TrCab
    WHERE c.CreadaTransferencia = 1 
      AND d.BodegaDestino = :almacenTran
      AND CONVERT(date, c.FechaCreacion) = :fecha
    GROUP BY c.Id, c.NumTransferencia, c.NumTransferenciaNva, c.FechaIntegracion,  c.Responsable
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ":almacenTran" => $almacenTran,
    ":fecha" => $fecha
]);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong class="card-title">Historial transferencias procesadas - <?php echo $almacenTran; ?></strong>
                
                <!-- Formulario de búsqueda por día -->
                <form method="get" class="form-inline">
                    <label class="mr-2">Fecha de picking:</label>
                    <input type="date" name="fecha" class="form-control mr-2" value="<?= htmlspecialchars($fecha) ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>
            <div class="card-body">
                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Transferencia</th>
                            <th>Nueva Transferencia</th>
                            <th>Cantidad</th>
                            <th>Escaneados</th>
                            <th>Fecha integración</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php foreach ($resumen as $r): ?>
                            <tr>
                                <td><?= $r->Id ?></td>
                                <td><?= $r->NumTransferencia ?></td>
                                <td><?= $r->NumTransferenciaNva ?></td>
                                <td><?= (int)$r->Cantidad ?></td>
                                <td><?= (int)$r->Escaneados ?></td>
                                <td><?= $r->FechaIntegracion ?></td>
                                <td><?= $r->Responsable ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($resumen)): ?>
                            <tr><td colspan="6" class="text-center">No hay datos para la fecha seleccionada.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>
