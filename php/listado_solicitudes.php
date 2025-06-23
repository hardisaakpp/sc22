
<?php
include_once "bd_StoreControl.php"; // o tu archivo de conexión a la base de datos

$desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-2 days'));
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$sentencia = $db->query("
                    SELECT 
                    *
                    FROM 
                        [SotCab_MT] AS T1
                    WHERE 
                        T1.DocDate BETWEEN '$desde' AND '$hasta' 
                        AND T1.Filler <> 'RL-SJ'
                        AND T1.DocStatus = 'O'
                    AND DocNum_Tr=0
                        AND NOT EXISTS (
                            SELECT 1
                            FROM [STORECONTROL].[dbo].[ced_groupsot] AS G
                            WHERE G.fk_docnumsotcab = T1.DocNum and [enabled] = 1)
                    ORDER BY T1.DocDate DESC
                        ");
$rows = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (count($rows) == 0) {
    echo '<h4>¡No existen registros!</h4>';
} else {
    echo '
    


<table id="tabla-solicitudes" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>FECHA</th>
                    <th>SOLICITUD</th>
                    <th>ORIGEN</th>
                    <th>DESTINO</th>
                    <th>COMENTARIO</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($rows as $citem) {
        echo "<tr>
                
<td><input type='checkbox' class='seleccionar' data-id='{$citem->DocNum}'></td>

                <td>{$citem->DocDate}</td>
                <td>{$citem->DocNum}</td>
                <td>{$citem->Filler}</td>
                <td>{$citem->ToWhsCode}</td>
                <td>{$citem->Comments}</td>
              </tr>";
    }
    echo '</tbody></table>';
}
?>
