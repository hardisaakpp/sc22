<?php
include_once "php/bd_StoreControl.php";

$idcab = $_POST['idcab'] ?? null;
if (!$idcab) {
    echo '<div class="alert alert-warning">No hay datos</div>';
    exit;
}

$stmt = $db->prepare("SELECT * FROM [STORECONTROL].[dbo].[rep_det] WHERE fk_id_cab = ? AND Quantity > 0");
$stmt->execute([$idcab]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo '<div class="alert alert-info">No hay artículos solicitados.</div>';
    exit;
}

echo '<table id="tablaSolicitadosModal" class="table table-bordered table-sm">';
echo '<thead><tr>';
foreach (array_keys($rows[0]) as $col) {
    echo '<th>' . htmlspecialchars($col) . '</th>';
}
echo '</tr></thead><tbody>';
foreach ($rows as $row) {
    echo '<tr>';
    foreach ($row as $val) {
        echo '<td>' . htmlspecialchars($val) . '</td>';
    }
    echo '</tr>';
}
echo '</tbody></table>';
?>
<script>
$(function(){
    $('#tablaSolicitadosModal').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100]
    });
});
</script>
