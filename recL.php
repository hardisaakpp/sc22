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
/* Estilos responsivos que preservan funcionalidad de DataTables */
@media (max-width: 768px) {
    /* Hacer la tabla scrolleable horizontalmente en móviles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 8px;
    }
    
    /* Mejorar el diseño de la tabla en móviles sin romper DataTables */
    #bootstrap-data-table {
        min-width: 600px; /* Ancho mínimo para mantener funcionalidad */
        font-size: 14px;
    }
    
    #bootstrap-data-table th,
    #bootstrap-data-table td {
        padding: 8px 6px;
        white-space: nowrap;
    }
    
    /* Botones más grandes para touch */
    .btn-sm {
        min-height: 38px;
        min-width: 80px;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 6px;
    }
    
    /* Header de la tabla más compacto */
    #bootstrap-data-table thead th {
        font-size: 13px;
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    /* Mejorar controles de DataTables en móvil */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        min-height: 38px;
        font-size: 16px; /* Previene zoom en iOS */
        border-radius: 6px;
    }
    
    /* Paginación más táctil */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        min-height: 38px;
        min-width: 38px;
        margin: 2px;
        border-radius: 6px;
    }
    
    /* Info de la tabla más compacta */
    .dataTables_wrapper .dataTables_info {
        font-size: 12px;
        margin-top: 10px;
    }
    
    /* Indicador de scroll horizontal */
    .table-responsive::after {
        content: "← Desliza para ver más →";
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,123,255,0.8);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .table-responsive:hover::after {
        opacity: 1;
    }
}

@media (max-width: 480px) {
    /* Pantallas muy pequeñas */
    #bootstrap-data-table {
        font-size: 12px;
    }
    
    #bootstrap-data-table th,
    #bootstrap-data-table td {
        padding: 6px 4px;
    }
    
    .btn-sm {
        min-width: 70px;
        font-size: 12px;
        padding: 6px 8px;
    }
    
    /* Ocultar columnas menos importantes en pantallas muy pequeñas */
    .mobile-hide-sm {
        display: none;
    }
}

/* Mejoras para tablets */
@media (min-width: 769px) and (max-width: 1024px) {
    #bootstrap-data-table {
        font-size: 14px;
    }
    
    .btn-sm {
        min-height: 36px;
        padding: 7px 14px;
    }
}
</style>

<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Stock en Transitoria <?php echo $almacenTran; ?></strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bootstrap-data-table" class="table table-striped table-bordered">
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
                                    <td><?= $r->ItemCode . ' - ' . $r->Descripcion ?></td>
                                    <td><?= $r->CantidadAbierta ?></td>
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
