<?php
include_once "header.php";

if (isset($_GET["pIdAlmacen"])) {
    $whsCica = $_GET["pIdAlmacen"];
}

$pFecha = Date('Y-m-d');
if (isset($_GET["pFecha"])) {
    $pFecha = $_GET["pFecha"];
}

if ($whsCica == 0) {
    echo ('NO TIENE UNA TIENDA ASIGNADA PARA CIERRE DE CAJA');
} else {
    $auxCAJA = 0;

    if ($pFecha >= date('Y-m-d', strtotime('yesterday'))) {
        $sentencia = $db->query("
            EXEC sp_cic_sincSAPSingle '" . $whsCica . "', '" . $pFecha . "';
            EXEC sp_cic_createCajas '" . $whsCica . "', '" . $pFecha . "';
            EXEC sp_cicUs_create '" . $whsCica . "', '" . $pFecha . "';
        ");
        $cajas = $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    $senten2 = $db->query("SELECT * FROM almacen WHERE id=" . $whsCica);
    $TEMPa1 = $senten2->fetchObject();

    $almacenCica = $TEMPa1->cod_almacen;
    $nomealmacenCica = $TEMPa1->nombre;

    $s2 = $db->query("SELECT TOP 1 status, cerrado FROM CiC WHERE fk_ID_almacen=" . $whsCica . " AND fecha='" . $pFecha . "'");
    $stat = $s2->fetchObject();

    $estado = $stat->cerrado;

    $s1 = $db->query("
        SELECT * FROM cic 
        WHERE fk_ID_almacen=" . $whsCica . " AND fecha='" . $pFecha . "'
    ");
    $cajas = $s1->fetchAll(PDO::FETCH_OBJ);

    $sentencia = $db->query("
        SELECT q1.id AS almacen, q1.fecha, q1.CardName AS forPag, Q1.valSAP, q2.[valRec],
               q2.[valOnline], q1.[valPinpadOn] AS valPinpadOn, q2.[valPinpadOff] AS valMedianet,
               ((q2.valRec) + (q2.valPinpadOff) + (q1.valPinpadOn) + (q2.valOnline) - (q1.valSAP)) AS 'Diferencia'
        FROM (
            SELECT c.fecha, c.whsCode, a.id,
                   CASE 
                       WHEN c.CardName LIKE 'Nota de crédito' THEN 'Nota de Crédito'
                       WHEN c.CardName LIKE '%VISA' THEN 'Visa'
                       WHEN c.CardName LIKE '%MASTERCARD' THEN 'MasterCard'
                       WHEN c.CardName LIKE '%DISCOVER' THEN 'Diners'
                       WHEN c.CardName LIKE '%DINERS' THEN 'Diners'
                       WHEN c.CardName LIKE '%AMERICAN EXPRESS' THEN 'American Express'
                       WHEN c.CardName LIKE 'Efectivo - Venta' THEN 'EFECTIVO'
                       WHEN c.CardName LIKE 'Crédito directo - Venta' THEN 'CREDITO DIRECTO CREDICORP'
                       WHEN c.CardName LIKE 'Crédito directo - Pago de abono' THEN 'EFECTIVO'
                       ELSE c.CardName
                   END AS CardName,
                   SUM(Valor) AS 'valSAP',
                   SUM(valPinpadOn) AS 'valPinpadOn'
            FROM cicSAP c
            JOIN Almacen a ON a.cod_almacen = c.whsCode
            WHERE c.origen NOT LIKE 'H' AND a.id='" . $whsCica . "' AND c.fecha='" . $pFecha . "'
            GROUP BY a.id, c.CardName, c.fecha, c.whsCode
        ) q1
        JOIN (
            SELECT [fecha], [whsCode],
                   CASE 
                       WHEN CardName LIKE 'Nota de crédito' THEN 'Nota de Crédito'
                       WHEN CardName LIKE '%VISA' THEN 'Visa'
                       WHEN CardName LIKE '%MASTERCARD' THEN 'MasterCard'
                       WHEN CardName LIKE '%DISCOVER' THEN 'Diners'
                       WHEN CardName LIKE '%DINERS' THEN 'Diners'
                       WHEN CardName LIKE '%AMERICAN EXPRESS' THEN 'American Express'
                       WHEN CardName LIKE 'Efectivo - Venta' THEN 'EFECTIVO'
                       WHEN CardName LIKE 'Crédito directo - Venta' THEN 'CREDITO DIRECTO CREDICORP'
                       WHEN CardName LIKE 'Crédito directo - Pago de abono' THEN 'EFECTIVO'
                       ELSE CardName
                   END AS CardName,
                   SUM([valRec]) AS [valRec],
                   SUM([valOnline]) AS [valOnline],
                   0 AS [valPinpadOn],
                   SUM([valPinpadOff]) AS [valPinpadOff]
            FROM [dbo].[cicUs]
            GROUP BY [fecha], [whsCode], CardName
        ) q2
        ON q1.fecha = q2.fecha AND q1.whsCode = q2.whsCode AND q1.CardName = q2.CardName
    ");
    $consolidados = $sentencia->fetchAll(PDO::FETCH_OBJ);
}
?>

<div class="content">
    <div class="row">
        <?php foreach ($cajas as $user) { 
            $auxCAJA = $user->id; ?>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-3">
                                <a href="cicU.php?id=<?php echo $user->id ?>">
                                    <i class="pe-7s-browser"></i>
                                </a>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><?php echo $user->caja ?></div>
                                    <div><?php echo $user->responsable . "-" . $user->observacion ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($estado == 0) { ?>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="hcicImport.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <input name="tiendaTuremp" value='<?php echo $whsCica; ?>' hidden>
                                <input name="pFecha" value='<?php echo $pFecha; ?>' hidden>
                                <input type="file" accept=".xlsx" name="file" class="form-control" id="exampleInputFile" required>
                            </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-secondary btn-lg">
                            <i class="fa fa-upload"></i>&nbsp; CARGAR CIERRE HITELL
                        </button>
                    </div>
                        </form>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="col-md-12">
        <div class="card">
            <form id="frmConteo">
                <div class="card-header">
                    <?php echo "<strong>[" . $pFecha . "] CIERRE DE CAJA CONSOLIDADO DE " . $nomealmacenCica . " </strong>" ?>
                </div>
                <div class="card-body card-block">
                    <table id="resumentbl" class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>FORMA PAGO</th>
                                <th id='v1'>TOTAL CIERRE</th>
                                <th id='v2'>RECIBIDO</th>
                                <th id='v3'>ONLINE</th>
                                <th id='v4'>PINPAD</th>
                                <th id='v5'>DATAFAST/ MEDIANET</th>
                                <th id='v6'>DIFERENCIA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consolidados as $forpag) { ?>
                                <tr>
                                    <td><?php echo $forpag->forPag ?></td>
                                    <td class="valSAP"><?php echo $forpag->valSAP; ?></td>
                                    <td class="valRec"><?php echo $forpag->valRec; ?></td>
                                    <td class="valOnline"><?php echo $forpag->valOnline; ?></td>
                                    <td class="valPinpad"><?php echo $forpag->valPinpadOn; ?></td>
                                    <td class="valMedianet"><?php echo $forpag->valMedianet; ?></td>
                                    <?php 
                                    $difz = $forpag->Diferencia;
                                    if ($difz < 0) {
                                        echo '<td class="Diferencia" style="color:red;">' . $difz . ' </td>';
                                    } elseif ($difz > 0) {
                                        echo '<td class="Diferencia" style="color:green;">' . $difz . ' </td>';
                                    } else {
                                        echo '<td class="Diferencia">' . $difz . ' </td>';
                                    }
                                    ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-secondary btn-lg" onClick=window.open("<?php echo "cicPrint.php?id=" . $auxCAJA ?>", "demo", "toolbar=0,status=0,")>
                        <i class="fa fa-print"></i>&nbsp; Imprimir
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var tds = document.getElementById('resumentbl').getElementsByTagName('td');
        var sum = 0.0;
        var svalRec = 0.0;
        var svalOnline = 0.0;
        var svalPinpad = 0.0;
        var svalMedianet = 0.0;
        for (var i = 0; i < tds.length; i++) {
            if (tds[i].className == 'valSAP') {
                sum += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            } else if (tds[i].className == 'valRec') {
                svalRec += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            } else if (tds[i].className == 'valOnline') {
                svalOnline += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            } else if (tds[i].className == 'valPinpad') {
                svalPinpad += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            } else if (tds[i].className == 'valMedianet') {
                svalMedianet += isNaN(tds[i].innerHTML) ? 0 : parseFloat(tds[i].innerHTML);
            }
        }
        document.getElementById('resumentbl').innerHTML += '<tr class="table-secondary"><td>TOTAL:</td><td>' + sum.toFixed(2) + '</td><td>' + svalRec.toFixed(2) +
            '</td><td>' + svalOnline.toFixed(2) +
            '</td><td>' + svalPinpad.toFixed(2) +
            '</td><td>' + svalMedianet.toFixed(2) + '</td></tr>';
    </script>
</div>

<?php include_once "footer.php"; ?>