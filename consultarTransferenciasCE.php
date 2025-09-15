<?php
require_once "php/bd_StoreControl.php"; // solo conexión BD

if (!isset($_GET["idcab"])) {
    exit("Falta parámetro idcab");
}
$idcab = intval($_GET["idcab"]);

// buscar todas las solicitudes del grupo
$sql = "SELECT DISTINCT fk_docnumsotcab 
        FROM ced_groupsotCE 
        WHERE fk_idgroup = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$idcab]);
$solicitudes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// acumulador resultados
$rows = [];
foreach ($solicitudes as $sol) {
    $q = $db->prepare("EXEC sp_SAP_ConsultaNumTransferenciaCE :solicitud");
    $q->execute([":solicitud" => $sol]);
    $res = $q->fetchAll(PDO::FETCH_ASSOC);
    $rows = array_merge($rows, $res);
}

// limpiar buffer antes de cabeceras
if (ob_get_length()) ob_end_clean();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Transferencias_Grupo_$idcab.xls");
header("Pragma: no-cache");
header("Expires: 0");

// imprimir cabecera
if (!empty($rows)) {
    echo implode("\t", array_keys($rows[0])) . "\n";
    foreach ($rows as $r) {
        echo implode("\t", $r) . "\n";
    }
}
exit;
