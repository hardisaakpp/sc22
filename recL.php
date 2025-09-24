<?php
// Suprimir warnings y notices que pueden mostrar paths de archivos
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Iniciar output buffer para evitar output no deseado
ob_start();

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
// Limpiar cualquier output no deseado antes de incluir el header
ob_clean();

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
/* Fix para el espacio extra debajo del body */
html, body {
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow-x: hidden;
}

/* Layout principal usando flexbox para controlar altura */
#right-panel {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1 1 auto;
    padding-bottom: 20px !important;
}

.site-footer {
    flex-shrink: 0;
    margin-top: auto;
}

/* Prevenir espacios extra en tablas DataTables */
.dataTables_wrapper {
    margin-bottom: 15px !important;
}

.card:last-child {
    margin-bottom: 0 !important;
}

/* Estilos para la columna de descripción en todas las pantallas */
#bootstrap-data-table td:nth-child(4) {
    white-space: normal !important;
    word-wrap: break-word;
    word-break: break-word;
    max-width: 300px; /* Más ancho para acomodar texto del tamaño original */
    line-height: 1.4;
    vertical-align: top;
    padding: 10px 8px;
}

#bootstrap-data-table th:nth-child(4) {
    vertical-align: middle;
    text-align: center;
}

/* Mejorar el formato del ItemCode y Descripción */
.item-code {
    font-weight: bold;
    color: #007bff;
    font-size: inherit; /* Mantener tamaño original */
    display: block;
    margin-bottom: 3px;
}

.item-description {
    color: #666;
    font-size: inherit; /* Mantener tamaño original */
    line-height: 1.4;
}

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
    
    /* Ajustar la columna de descripción en móviles */
    #bootstrap-data-table td:nth-child(4) {
        max-width: 220px !important; /* Más ancho para texto del tamaño original */
        padding: 8px 6px !important;
    }
    
    .item-code {
        font-size: inherit !important; /* Mantener tamaño original */
        margin-bottom: 2px !important;
    }
    
    .item-description {
        font-size: inherit !important; /* Mantener tamaño original */
        line-height: 1.3 !important;
    }
    
    /* Botones más grandes para touch */
    .btn-sm {
        min-height: 38px;
        min-width: 80px;
        font-size: 14px;
        padding: 6px 10px; /* Reducido de 8px 12px */
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
    
    .navbar-header {
        position: relative;
    }
    
    .menutoggle {
        position: absolute;
        left: 10px; /* Ajusta esta distancia según necesites */
        top: 35%;
        transform: translateY(-50%);
        z-index: 1000;
    }
    
    .navbar-brand {
        margin-left: 50px; /* Espacio para el botón */
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
        padding: 5px 8px; /* Reducido ligeramente */
    }
    
    /* Ajustar descripción en pantallas muy pequeñas */
    #bootstrap-data-table td:nth-child(4) {
        max-width: 160px !important; /* Un poco más ancho para compensar el tamaño de texto */
    }
    
    .item-code {
        font-size: inherit !important; /* Mantener tamaño original */
        margin-bottom: 1px !important;
    }
    
    .item-description {
        font-size: inherit !important; /* Mantener tamaño original */
        line-height: 1.2 !important;
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
        padding: 6px 12px; /* Reducido de 7px 14px */
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
                                    <td>
                                        <span class="item-code"><?= $r->ItemCode ?></span>
                                        <span class="item-description"><?= $r->Descripcion ?></span>
                                    </td>
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

// Mejorar la visualización de las descripciones largas
$(document).ready(function() {
    // Agregar tooltips para descripciones que se corten
    $('.item-description').each(function() {
        var $this = $(this);
        if (this.scrollHeight > this.clientHeight) {
            $this.attr('title', $this.text());
        }
    });
});
</script>

<?php include_once "footer.php"; ?>
