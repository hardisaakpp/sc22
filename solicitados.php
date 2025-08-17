<?php
// filepath: c:\xampp\htdocs\sc22\solicitados.php
include_once "php/bd_StoreControl.php";
$idcab = $_GET['idcab'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artículos Solicitados</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h4>Artículos Solicitados</h4>
    <?php
    if (!$idcab) {
        echo '<div class="alert alert-warning">No hay datos</div>';
    } else {
        $stmt = $db->prepare("SELECT * FROM [STORECONTROL].[dbo].[rep_det] WHERE fk_id_cab = ? AND Quantity > 0");
        $stmt->execute([$idcab]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            echo '<div class="alert alert-info">No hay artículos solicitados.</div>';
        } else {
            echo '<table id="tablaSolicitados" class="table table-bordered table-sm">';
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
        }
    }
    ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
    // Solo inicializar DataTable si existe la tabla
    if ($('#tablaSolicitados').length) {
        $('#tablaSolicitados').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100]
        });
    }
});
</script>
</body>
</html>
