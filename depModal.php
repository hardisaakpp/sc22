<?php
include_once "php/bd_StoreControl.php";
header('Content-Type: text/html');

$input = json_decode(file_get_contents('php://input'), true);
$fecha = $input['fecha'] ?? null;
$whsCode = $input['whsCode'] ?? null;

if (!$fecha || !$whsCode) {
    echo "<div class='alert alert-warning'>Parámetros incompletos.</div>";
    exit;
}

$sql1 = "
    SELECT d.Id,
        d.U_WhsCode,
        d.U_Fecha AS FechaCierre,
        d.DepositDate,
        c.FormatCode,
        c.AcctName,
        d.U_Ref_Bancar,
        d.TotalLC,
        d.creadoSAP AS integrado
    FROM DepositosTiendas d
    JOIN CuentaFinanciera c ON d.DepositAccount = c.AcctCode
    WHERE d.U_WhsCode = ? AND d.U_Fecha = ?
";
$stmt1 = $db->prepare($sql1);
$stmt1->execute([$whsCode, $fecha]);
$depositos = $stmt1->fetchAll(PDO::FETCH_OBJ);

// Total
$totalDepositos = array_reduce($depositos, fn($sum, $d) => $sum + $d->TotalLC, 0);

// Efectivo
$sql2 = "
    SELECT SUM(c.valRec) AS Efectivo
    FROM cicUs c
    WHERE c.whsCode = ? AND c.fecha = ?
      AND (c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%' OR c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%')
";
$stmt2 = $db->prepare($sql2);
$stmt2->execute([$whsCode, $fecha]);
$efectivo = $stmt2->fetch(PDO::FETCH_OBJ)->Efectivo ?? 0;
$diferencia = $totalDepositos - $efectivo;
$clase = ($diferencia == 0) ? 'success' : 'danger';
?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Cierre</th>
            <th>Fecha Depósito</th>
            <th>Cuenta</th>
            <th>Nombre Cuenta</th>
            <th>Referencia Bancaria</th>
            <th>Total</th>
            <th>Integrado SAP</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($depositos as $d): ?>
            <tr>
                <td><?= $d->Id ?></td>
                <td><?= $d->FechaCierre ?></td>
                <td><?= $d->DepositDate ?></td>
                <td><?= $d->FormatCode ?></td>
                <td><?= $d->AcctName ?></td>
                <td><?= $d->U_Ref_Bancar ?></td>
                <td><?= number_format($d->TotalLC, 2) ?></td>
                <td><?= $d->integrado ? 'Sí' : 'No' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($depositos)): ?>
            <tr><td colspan="8" class="text-center">No hay depósitos registrados.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" class="text-right">Total Depósitos:</th>
            <th colspan="2"><?= number_format($totalDepositos, 2) ?></th>
        </tr>
        <tr>
            <th colspan="6" class="text-right">Efectivo (cicUs):</th>
            <th colspan="2"><?= number_format($efectivo, 2) ?></th>
        </tr>
        <tr class="table-<?= $clase ?>">
            <th colspan="6" class="text-right">Diferencia:</th>
            <th colspan="2"><?= number_format($diferencia, 2) ?></th>
        </tr>
    </tfoot>
</table>
