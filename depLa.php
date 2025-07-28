<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-d', strtotime('-7 days'));
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');
$marcaSeleccionada = $_GET["marca"] ?? "Todos";

// 1. OBTENER LISTA DE MARCAS
$sqlMarcas = "SELECT DISTINCT a.[marca] FROM Almacen a WHERE a.fk_emp = 'MT' ORDER BY a.[marca]";
$stmtMarcas = $db->prepare($sqlMarcas);
$stmtMarcas->execute();
$listaMarcas = $stmtMarcas->fetchAll(PDO::FETCH_COLUMN);

$sql = "
SELECT q1.fecha, q1.whsCode, q1.Efectivo, q2.Efectivo AS Depositado,
       (q1.Efectivo - ISNULL(q2.Efectivo, 0)) AS Diferencia,
       q2.PendienteSAP, q1.marca
FROM (
    SELECT c.fecha, c.whsCode, SUM(c.valRec) AS Efectivo, ISNULL(a.[marca], '') AS marca
    FROM cicUs c
    JOIN Almacen a ON c.whsCode = a.cod_almacen
    WHERE c.fecha BETWEEN :f1 AND :f2
      AND a.fk_emp = 'MT'
      AND (
            c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%'
         OR c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%'
      )
";

if ($marcaSeleccionada !== "Todos") {
    $sql .= " AND ISNULL(a.[marca], '') = :marca ";
}

$sql .= "
    GROUP BY c.fecha, c.whsCode, ISNULL(a.[marca], '')
) q1
LEFT JOIN (
    SELECT U_Fecha, U_WhsCode, SUM(TotalLC) AS Efectivo, 
           SUM(CASE WHEN creadoSAP = 0 THEN 1 ELSE 0 END) AS PendienteSAP
    FROM DepositosTiendas d
    WHERE d.U_Fecha BETWEEN :f3 AND :f4
    GROUP BY d.U_Fecha, d.U_WhsCode
) q2 ON q1.whsCode = q2.U_WhsCode AND q1.fecha = q2.U_Fecha
";


$stmt = $db->prepare($sql);

// 3. BINDEAR PARÁMETROS
$params = [
    ":f1" => $fechaInicio,
    ":f2" => $fechaFin,
    ":f3" => $fechaInicio,
    ":f4" => $fechaFin
];

if ($marcaSeleccionada !== "Todos") {
    $params[":marca"] = $marcaSeleccionada;
}

$stmt->execute($params);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);

// Acumuladores
$totalEfectivo = 0;
$totalDepositado = 0;
$totalDiferencia = 0;
$totalPendienteSAP = 0;
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

                    <!-- FILTRO DE MARCA -->
                    <label class="mr-2">Marca:</label>
                    <select name="marca" class="form-control mr-2">
                        <option value="Todos">Todos</option>
                        <?php foreach ($listaMarcas as $marca): ?>
                            <option value="<?= htmlspecialchars($marca) ?>" <?= ($marca == $marcaSeleccionada) ? "selected" : "" ?>>
                                <?= htmlspecialchars($marca) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                </form>
            </div>

            <div class="card-body">
               

                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>WhsCode</th>
                            <th>Efectivo (Cierre)</th>
                            <th>Depositado</th>
                            <th>Diferencia</th>
                            <th>Por Integrar</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php foreach ($resumen as $r): ?>
                       <?php
                       $totalEfectivo += $r->Efectivo;
                       $totalDepositado += ($r->Depositado ?? 0);
                       $totalDiferencia += $r->Diferencia;
                       $totalPendienteSAP += $r->PendienteSAP;
                       ?>
                        <tr>
                            <td><?= $r->fecha ?></td>
                            <td><?= $r->whsCode ?></td>
                            <td><?= number_format($r->Efectivo, 2) ?></td>
                            <td>
                                <a href="depD.php?fecha=<?= urlencode($r->fecha) ?>&whsCode=<?= urlencode($r->whsCode) ?>&pflag=1" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <?= number_format($r->Depositado ?? 0, 2) ?>
                                </a>
                            </td>
                            <td>
                                <?php 
                                if ($r->Diferencia != 0) {
                                    echo "<span class='text-danger'>" . number_format($r->Diferencia, 2) . "</span>";
                                } else {
                                    echo "<span class='text-success'>" . number_format($r->Diferencia, 2) . "</span>";
                                }
                                ?>
                            </td>
                            <td>
                              
                                <?php 
                                if ($r->PendienteSAP > 0) {
                                    echo '
                                      <a href="#" class="btn btn-sm btn-warning"> 
                                      '. number_format($r->PendienteSAP ?? 0, 0) .'
                                        </a>
                                    ';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($resumen)): ?>
                            <tr><td colspan="6" class="text-center">No hay datos para el rango seleccionado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align: right;">Totales:</th>
                            <th><?= number_format($totalEfectivo, 2) ?></th>
                            <th><?= number_format($totalDepositado, 2) ?></th>
                            <th><?= number_format($totalDiferencia, 2) ?></th>
                            <th><?= number_format($totalPendienteSAP, 0) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function abrirVentanaPopup(url) {
    window.open(
        url,
        '_blank',
        'width=1000,height=700,scrollbars=yes,toolbar=no,location=no,status=no,menubar=no'
    );
    return false;
}
</script>

<?php include_once "footer.php"; ?>
