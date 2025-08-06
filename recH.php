
<?php

include_once "header.php";
include_once "php/bd_StoreControl.php";




$whsCica = $_SESSION["whsCica"] ?? null;
$whsTr = $_SESSION["whsTr"] ?? null;

if (empty($whsCica) || empty($whsTr) || $whsCica <= 0 || $whsTr <= 0) {
    exit("El usuario no tiene configurados los almacenes.");
}


    $s11 = $db->query("
       SELECT cod_almacen FROM almacen WHERE id = '".$whsTr."' ");
    $TEMPa1 = $s11->fetchObject();
        $almacenTran = $TEMPa1->cod_almacen;



    $s10 = $db->query("
       SELECT cod_almacen FROM almacen WHERE id = '".$whsCica."' ");
    $TEMPa2 = $s10->fetchObject();
        $almacenR = $TEMPa2->cod_almacen;

// Consulta resumen por fecha

$sql = "

SELECT c.Id, c.NumTransferencia, sum(d.CantidadAbierta) as Cantidad, SUM(D.Scan) AS Escaneados, c.FechaIntegracion

  FROM [dbo].[TransferenciasCabecera] c join TransferenciasDetalle d on c.Id=d.id_TrCab
  where c.CreadaTransferencia=1 and d.BodegaDestino='".$almacenTran."'
  GROUP BY c.Id, c.NumTransferencia,c.FechaIntegracion
      
";

$stmt = $db->prepare($sql);
$stmt->execute();
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Historial tranferencias procesadas - <?php echo $almacenTran; ?></strong>
               

            </div>
            <div class="card-body">
                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Transferencia</th>
                            <th>Cantidad</th>
                            <th>Escaneados</th>
                            <th>Fecha recepcion</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php foreach ($resumen as $r): ?>
                            <tr>
                                <td><?= $r->Id ?></td>
                                <td><?= $r->NumTransferencia ?></td>
                                <td><?= $r->Cantidad ?></td>
                                <td><?= $r->Escaneados ?></td>
                                <td><?= $r->FechaIntegracion ?></td>
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
