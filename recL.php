<?php
session_start(); // Siempre antes que cualquier salida

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["numTransferencia"])) {
    require_once "php/bd_StoreControl.php"; // Conexión sin incluir header.php todavía

    $numTransferencia = (int)$_POST["numTransferencia"];
    $idUsuario = $_SESSION["idU"] ?? null;

    if (!$idUsuario) {
        exit("Usuario no autenticado.");
    }

    // Ejecutar SP y obtener ID
    $sql = "EXEC dbo.sp_RegistrarTransferenciaConDetalle @NumTransferencia = ?, @Usuario = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$numTransferencia, $idUsuario]);

    // Obtener el ID retornado por el SP
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $idTransferenciaCabecera = $resultado['IdTransferenciaCabecera'] ?? null;

    // Redirigir con el ID obtenido
    if ($idTransferenciaCabecera) {
        header("Location: recP.php?idcab=" . $idTransferenciaCabecera);
        exit;
    } else {
        exit("No se pudo obtener el ID de la cabecera creada.");
    }

}
?>


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
EXEC sp_sap_ActualizarTransferencias @WhsCode = '".$almacenTran."';

";

$stmt = $db->prepare($sql);
$stmt->execute();
$sql = "

SELECT [Id]
      ,[NumTransferencia]
      ,[Fecha]
      ,[ItemCode]
      ,[CodeBars]
      ,[Descripcion]
      ,[Quantity]
      ,[CantidadAbierta]
      ,[BodegaOrigen]
      
from TransferenciasSAP
where BodegaDestino='".$almacenTran."' and 
    numTransferencia not in (
SELECT [NumTransferencia]

  FROM [dbo].[TransferenciasCabecera]
  where CreadaTransferencia=0 ) and CantidadAbierta > 0
";

$stmt = $db->prepare($sql);
$stmt->execute();
$resumen = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Stock en Transitoria <?php echo $almacenTran; ?></strong>
               

            </div>
            <div class="card-body">
                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Transferencia</th>
                            <th>BodegaOrigen</th>
                            <th>Fecha</th>
                            <th>ItemCode</th>
                            <th>Descripcion</th>
                            <th>CantidadAbierta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumen as $r): ?>
                            <tr>
                                <td>
                                    <form method="post" action="" style="display:inline;" onsubmit="return confirmarRecepcion(<?= $r->NumTransferencia ?>, '<?= $r->Fecha ?>')">
                                        <input type="hidden" name="numTransferencia" value="<?= $r->NumTransferencia ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <?= $r->NumTransferencia ?>
                                        </button>
                                    </form>
                                </td>

                                <td><?= $r->BodegaOrigen ?></td>
                                <td><?= $r->Fecha ?></td>
                                <td><?= $r->ItemCode ?></td>
                                <td><?= $r->Descripcion ?></td>
                                <td><?= $r->CantidadAbierta ?></td>
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
<script>
function confirmarRecepcion(num, fecha) {
    return confirm("¿Quiere proceder a recibir la transferencia número " + num + " de fecha " + fecha + "?");
}
</script>

<?php include_once "footer.php"; ?>
