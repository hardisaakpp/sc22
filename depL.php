<?php
include_once "header.php";
include_once "php/bd_StoreControl.php";


$whsCica = $_SESSION["whsCica"] ?? null;


// Obtener nombre del almacén
$alm = $db->prepare("SELECT cod_almacen FROM almacen WHERE id = ?");
$alm->execute([$whsCica]);
$almacen = $alm->fetch(PDO::FETCH_OBJ);

$fechaInicio = $_GET["fechaInicio"] ?? date('Y-m-01');
$fechaFin = $_GET["fechaFin"] ?? date('Y-m-t');

// Consulta resumen por fecha
$sql = "

select q1.fecha, q1.whsCode, q1.Efectivo, q2.Efectivo as Depositado,
       (q1.Efectivo - ISNULL(q2.Efectivo,0)) as Diferencia,
       (q2.PendienteSAP) as PendienteSAP
from
	(
	SELECT c.fecha, c.whsCode, sum(c.valRec) AS Efectivo
	FROM cicUs c
	WHERE c.whsCode = ?  AND c.fecha BETWEEN ? AND ?
		AND
		  ( c.CardName COLLATE Latin1_General_CI_AI LIKE '%Efectivo%'
		  or c.CardName COLLATE Latin1_General_CI_AI LIKE '%Abono%')
	GROUP BY c.fecha, c.whsCode
	) q1
	left join
	(
	select U_Fecha,U_WhsCode, sum(TotalLC) AS Efectivo, 
           sum(CASE WHEN creadoSAP = 0 THEN 1 ELSE 0 END) as PendienteSAP
	from DepositosTiendas d
	where U_WhsCode = ?  AND d.U_Fecha BETWEEN ? AND ?
	GROUP BY d.U_Fecha, d.U_WhsCode
	) q2 on q1.whsCode=q2.U_WhsCode and q1.fecha=q2.U_Fecha

";

$stmt = $db->prepare($sql);
$stmt->execute([$almacen->cod_almacen, $fechaInicio, $fechaFin,$almacen->cod_almacen, $fechaInicio, $fechaFin]);
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
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
                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
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
                            <tr>
                                <td><?= $r->fecha ?></td>
                                <td><?= $r->whsCode ?></td>
                                <td><?= number_format($r->Efectivo, 2) ?></td>
                                <td>
                                    <a href="depD.php?fecha=<?= urlencode($r->fecha) ?>&whsCode=<?= urlencode($r->whsCode) ?>" class="btn btn-sm btn-info">    
                                        <?= number_format($r->Depositado, 2) ?>
                                    </a> 
                                </td>
                                
                                <td>
                                    


                                    
                                    <?php 
                                    if ($r->Diferencia <> 0) {
                                        echo "<span class='text-danger'>" . number_format($r->Diferencia, 2) . "</span>";
                                    } else {
                                        echo "<span class='text-success'>" . number_format($r->Diferencia, 2) . "</span>" ;
                                    };
                                 ?>
                               
                                </td>


                                <td><?php 
                                if ($r->PendienteSAP > 0) {
                                     echo "<span class='text-danger'>" . number_format($r->PendienteSAP, 0) . "</span>";
                                } ;
                                 ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($resumen)): ?>
                            <tr><td colspan="6" class="text-center">No hay datos para el rango seleccionado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>
