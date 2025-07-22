<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";


$whsCica = $_SESSION["whsCica"] ?? null;

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-01');
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');

// Consulta resumen por fecha
$sql = "
SELECT 
    c.fecha,
    c.whsCode,
    SUM(c.valRec) AS Efectivo,
    ISNULL(SUM(d.TotalLC), 0) AS Depositado,
    SUM(c.valRec) - ISNULL(SUM(d.TotalLC), 0) AS Diferencia,
    (
        SELECT ISNULL(SUM(TotalLC), 0)
        FROM DepositosTiendas dt
        WHERE dt.U_WhsCode = c.whsCode AND dt.U_Fecha = c.fecha AND dt.creadoSAP = 0
    ) AS PendienteSAP
FROM cicUs c
JOIN Almacen a ON c.whsCode =  a.cod_almacen
LEFT JOIN DepositosTiendas d
    ON c.whsCode = d.U_WhsCode AND c.fecha = d.U_Fecha
WHERE a.id = ? AND c.fecha BETWEEN ? AND ?
GROUP BY c.fecha, c.whsCode
ORDER BY c.fecha DESC
";

$stmt = $db->prepare($sql);
$stmt->execute([$whsCica, $fechaInicio, $fechaFin]);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="content">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Resumen de Depósitos</strong>
                <form method="GET" class="form-inline float-right">
                    <label class="mr-2">Desde:</label>
                    <input type="date" name="fechaInicio" value="<?= $fechaInicio ?>" class="form-control mr-2">
                    <label class="mr-2">Hasta:</label>
                    <input type="date" name="fechaFin" value="<?= $fechaFin ?>" class="form-control mr-2">
                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>WhsCode</th>
                            <th>Efectivo (cicUs)</th>
                            <th>Depositado</th>
                            <th>Diferencia</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen as $r): ?>
                            <tr>
                                <td><?= $r->fecha ?></td>
                                <td><?= $r->whsCode ?></td>
                                <td><?= number_format($r->Efectivo, 2) ?></td>
                                <td><?= number_format($r->Depositado, 2) ?></td>
                                <td><?= number_format($r->Diferencia, 2) ?></td>
                                <td><?php 
                                if ($r->PendienteSAP > 0) {
                                     echo "<span class='text-danger'>" . number_format($r->PendienteSAP, 2) . "</span>";
                                } ;
                                 ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($resumen)): ?>
                            <tr><td colspan="6" class="text-center">No hay datos para el rango seleccionado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>
