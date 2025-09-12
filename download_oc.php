<?php
include_once "php/bd_StoreControl.php";

if (isset($_POST['oc'])) {
    $oc = $_POST['oc'];

    // Solo los campos que existen en ODC_sap
    $sentencia = $db->query("SELECT DocNum, LineNum, ItemCode, Quantity, WhsCode, DocEntry
                             FROM ODC_sap 
                             WHERE DocNum = $oc");
    $rows = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=OC_$oc.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Cabecera
        echo "DocNum\tLineNum\tItemCode\tQuantity\tWhsCode\tBaseType\tBaseEntry\tBaseLine\n";

        // Filas
        foreach ($rows as $r) {
            $baseType  = 22;
            $baseEntry = $r['DocEntry'];
            $baseLine  = $r['LineNum'];

            echo $r['DocNum']."\t".$r['LineNum']."\t".$r['ItemCode']."\t".$r['Quantity']."\t".
                 $r['WhsCode']."\t".$baseType."\t".$baseEntry."\t".$baseLine."\n";
        }
    } else {
        echo "No hay registros para la OC $oc";
    }
} else {
    echo "OC no especificada.";
}
