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

<style>
/* Estilos responsivos para móviles */
@media (max-width: 768px) {
    .mobile-card-view {
        display: block !important;
    }
    
    .mobile-card-view thead,
    .mobile-card-view tbody,
    .mobile-card-view th,
    .mobile-card-view td,
    .mobile-card-view tr {
        display: block;
    }
    
    .mobile-card-view thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    .mobile-card-view tr {
        border: 1px solid #ccc;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .mobile-card-view td {
        border: none;
        position: relative;
        padding: 8px 8px 8px 120px !important;
        text-align: left;
        white-space: normal;
        font-size: 14px;
    }
    
    .mobile-card-view td:before {
        content: attr(data-label) ": ";
        position: absolute;
        left: 8px;
        width: 100px;
        font-weight: bold;
        color: #333;
        text-align: left;
    }
    
    .mobile-btn-transfer {
        width: 100% !important;
        padding: 12px !important;
        font-size: 16px !important;
        margin-bottom: 8px;
        border-radius: 6px;
        font-weight: bold;
    }
    
    .card-title-mobile {
        font-size: 18px;
        text-align: center;
        margin-bottom: 15px;
    }
}

@media (max-width: 480px) {
    .content {
        padding: 10px 5px;
    }
    
    .card {
        margin: 0 5px;
        border-radius: 10px;
    }
    
    .card-body {
        padding: 15px 10px;
    }
}

/* Mejoras para tablets */
@media (min-width: 769px) and (max-width: 1024px) {
    .table-responsive {
        font-size: 14px;
    }
    
    .btn {
        padding: 8px 12px;
    }
}

/* Scroll horizontal para pantallas medianas cuando sea necesario */
@media (min-width: 769px) {
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}
</style>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title card-title-mobile">Stock en Transitoria <?php echo $almacenTran; ?></strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bootstrap-data-table" class="table table-striped table-bordered mobile-card-view">
                        <thead>
                            <tr>
                                <th>Transferencia</th>
                                <th>BodegaOrigen</th>
                                <th>Fecha</th>
                                <th>ItemCode - Descripcion</th>
                                <th>CantidadAbierta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resumen as $r): ?>
                                <tr>
                                    <td data-label="Transferencia">
                                        <form method="post" action="" style="display:inline;" onsubmit="return confirmarRecepcion(<?= $r->NumTransferencia ?>, '<?= $r->Fecha ?>')">
                                            <input type="hidden" name="numTransferencia" value="<?= $r->NumTransferencia ?>">
                                            <button type="submit" class="btn btn-primary btn-sm mobile-btn-transfer">
                                                <?= $r->NumTransferencia ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td data-label="Bodega Origen"><?= $r->BodegaOrigen ?></td>
                                    <td data-label="Fecha"><?= $r->Fecha ?></td>
                                    <td data-label="Producto"><?= $r->ItemCode . ' - ' . $r->Descripcion ?></td>
                                    <td data-label="Cantidad"><?= $r->CantidadAbierta ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($resumen)): ?>
                                <tr><td colspan="5" class="text-center">No hay datos para el rango seleccionado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
