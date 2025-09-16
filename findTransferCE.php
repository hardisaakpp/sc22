<?php
include_once "header.php";

$numeroSolicitud = $_POST["numeroSolicitud"] ?? null;
$resultados = [];

if ($numeroSolicitud) {
    try {
        // Ejecuta el SP
        $stmt = $db->prepare("EXEC sp_SAP_ConsultaNumTransferenciaCE :numSolicitud");
        $stmt->bindParam(":numSolicitud", $numeroSolicitud, PDO::PARAM_INT);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<style>
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
</style>

<div class="content">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <strong>Consulta Transferencia por Solicitud</strong>
            </div>
            <div class="card-body card-block">

                <form method="POST" class="form-inline" style="gap:10px;">
                    <input type="number" 
                           name="numeroSolicitud" 
                           class="form-control" 
                           placeholder="Ingrese N° Solicitud" 
                           required 
                           value="<?= htmlspecialchars($numeroSolicitud ?? '') ?>">
                    <button type="submit" class="btn btn-outline-primary">🔍 Consultar</button>
                </form>

                <?php if ($numeroSolicitud && empty($resultados) && empty($error)): ?>
                    <div class="alert alert-warning mt-3">⚠️ No se encontraron resultados para la solicitud <?= htmlspecialchars($numeroSolicitud) ?>.</div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mt-3">❌ Error: <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($resultados)): ?>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>DocNum Solicitud</th>
                                <th>DocNum Transferencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row->docnum_sot) ?></td>
                                    <td><?= htmlspecialchars($row->docnum_transferencia) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div>

<?php include_once "footer.php"; ?>
