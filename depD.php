<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";

$fecha = $_GET["fecha"] ?? $_POST["fecha"] ?? null;
$whsCode = $_GET["whsCode"] ?? $_POST["whsCode"] ?? null;

if (!$fecha || !$whsCode) {
    echo "<h4>Parámetros incompletos.</h4>";
    exit();
}

// 1. Obtener depósitos
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

// 2. Total de depósitos
$totalDepositos = 0;
foreach ($depositos as $d) {
    $totalDepositos += $d->TotalLC;
}

// 3. Obtener efectivo desde cicUs
$sql2 = "
    SELECT SUM(c.valRec) AS Efectivo
    FROM cicUs c
    WHERE c.whsCode = ? AND c.fecha = ?
      AND (c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%'
           OR c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%')
";
$stmt2 = $db->prepare($sql2);
$stmt2->execute([$whsCode, $fecha]);
$efectivo = $stmt2->fetch(PDO::FETCH_OBJ)->Efectivo ?? 0;

// 4. Calcular diferencia
$diferencia = $totalDepositos - $efectivo;
$clase = ($diferencia == 0) ? 'success' : 'danger';
?>

<div class="content">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Detalle de Depósitos - <?= $whsCode ?> - <?= $fecha ?></strong>
            </div>
            <div class="card-body">
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
                            <th>Acción</th>

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
                                <td>
                                    <?php if (!$d->integrado): ?>
                                        <button class="btn btn-sm btn-warning" onclick="integrarDepositoPorId(<?= $d->Id ?>)">Integrar</button>
                                    <?php else: ?>
                                        <span class="text-success">✔</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        <?php if (empty($depositos)): ?>
                            <tr><td colspan="7" class="text-center">No hay depósitos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total Depósitos:</th>
                            <th colspan="2"><?= number_format($totalDepositos, 2) ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Efectivo (cicUs):</th>
                            <th colspan="2"><?= number_format($efectivo, 2) ?></th>
                        </tr>
                        <tr class="table-<?= $clase ?>">
                            <th colspan="5" class="text-right">Diferencia:</th>
                            <th colspan="2"><?= number_format($diferencia, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
                <a href="depL.php" class="btn btn-secondary mt-3">← Volver al resumen</a>
                <a href="depC.php?U_Fecha=<?php echo $fecha; ?>"  class="btn btn-primary mt-3">    
                                        Nuevo
                                    </a> 
            </div>
        </div>
    </div>
</div>

<script>
function integrarDeposito(whsCode, fecha, total) {
    if (confirm(`¿Estás seguro de integrar el depósito del ${fecha} (${whsCode}) por $${total}?`)) {
        fetch('enviar_deposito_sap.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ whsCode, fecha })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Depósito integrado correctamente.');
                location.reload();
            } else {
                alert('Error al integrar: ' + (data.error || 'Respuesta inesperada'));
            }
        })
        .catch(err => alert('Error de red: ' + err));
    }
}


function integrarDepositoPorId(id) {
    if (confirm("¿Estás seguro de integrar este depósito?")) {
        fetch('enviar_deposito_sap.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Depósito integrado correctamente.');
                location.reload();
            } else {
                alert('Error al integrar: ' + (data.error || 'Respuesta inesperada'));
            }
        })
        .catch(err => alert('Error de red: ' + err));
    }
}


</script>


<?php include_once "footer.php"; ?>
