<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-d', strtotime('-7 days'));
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');

$sql = "
SELECT TOP (1000) d.Id, d.DepositDate AS FechaDeposito, c.AcctName, d.TotalLC,
       d.U_Fecha AS FechaCierre, d.U_WhsCode, d.U_Ref_Bancar AS NumeroDeposito,
       d.Responsable, d.creadoSAP, a.marca
FROM DepositosTiendas d
JOIN almacen a on d.U_WhsCode = a.cod_almacen
JOIN CuentaFinanciera c ON d.DepositAccount = c.AcctCode
WHERE d.U_Fecha BETWEEN ? AND ?
";

$stmt = $db->prepare($sql);
$stmt->execute([$fechaInicio, $fechaFin]);
$detalle = $stmt->fetchAll(PDO::FETCH_OBJ);

$totalDepositado = 0;
?>

<div class="content">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Detalle de Depósitos</strong>
                <form method="GET" class="form-inline float-right">
                    <label class="mr-2">Desde:</label>
                    <input type="date" name="fechaInicio" value="<?= $fechaInicio ?>" class="form-control mr-2">
                    <label class="mr-2">Hasta:</label>
                    <input type="date" name="fechaFin" value="<?= $fechaFin ?>" class="form-control mr-2">
                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
<a href="exportar_detalle_csv.php?fechaInicio=<?= urlencode($fechaInicio) ?>&fechaFin=<?= urlencode($fechaFin) ?>" 
   class="btn btn-sm btn-success" target="_blank">
   ⬇ Descargar CSV
</a>


                </form>
            </div>
            <div class="card-body">
                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha Depósito</th>
                            <th>Cuenta</th>
                            <th>Monto Depositado</th>
                            <th>Fecha Cierre</th>
                            <th>Almacén</th>
                            <th>Número Depósito</th>
                            <th>Responsable</th>
                            <th>Creado en SAP</th>
                            <th>Marca</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $r): ?>
                        <?php $totalDepositado += $r->TotalLC; ?>
                        <tr>
                            <td><?= $r->FechaDeposito ?></td>
                            <td><?= $r->AcctName ?></td>
                            <td><?= number_format($r->TotalLC, 2) ?></td>
                            <td><?= $r->FechaCierre ?></td>
                            <td><?= $r->U_WhsCode ?></td>
                            <td><?= $r->NumeroDeposito ?></td>
                            <td><?= $r->Responsable ?></td>
                            <td><?= $r->creadoSAP ? 'Sí' : 'No' ?></td>
                            <td><?= $r->marca ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($detalle)): ?>
                            <tr><td colspan="9" class="text-center">No hay datos para el rango seleccionado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Totales</th>
                            <th><?= number_format($totalDepositado, 2) ?></th>
                            <th colspan="6"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>
