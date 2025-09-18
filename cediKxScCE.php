<?php
include_once "header.php";

// Si no es admin no abre


    // Fecha por defecto: 2 días atrás
    $desde = date('Y-m-d', strtotime('-2 days'));
    $hasta = date('Y-m-d');

    if (isset($_POST["desde"]) && isset($_POST["hasta"])) {
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];
    }

    // Consulta con JOINs
    $sentencia = $db->query("
        SELECT 
            S.[fk_idgroup] AS GRUPO,
            G.[estado],
            G.fecha_creacion AS FECPROCESADO,
            U.username AS USUARIO,
            G.comentario AS REF,
            S.[fk_docnumsotcab] AS SOLICITUD,
            S.[DocDate] AS FECSOLICITUD,
            S.[Filler] AS ORIGEN,
            S.[ToWhsCode] AS DESTINO,
            S.[Comments]
        FROM [STORECONTROL].[dbo].[ced_groupsotCE] S
        JOIN [STORECONTROL].[dbo].[ced_groupCE] G ON S.fk_idgroup = G.id
        JOIN [STORECONTROL].[dbo].[users] U ON G.fk_iduser = U.id
        WHERE S.DocDate BETWEEN '".$desde."' AND '".$hasta."'
    ");

    $rows = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<!-- Breadcrumbs-->
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>GRUPOS DE SOLICITUDES</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php';">X</button>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
<!-- /.breadcrumbs-->

<div class="content">

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form id="monthformX" method="post" action="">
                <div class="input-group">
                    Rango fecha
                    <input type="date" name="desde" id="desde" class="form-control" value="<?php echo $desde ?>" required>
                    <input type="date" name="hasta" id="hasta" class="form-control" value="<?php echo $hasta ?>" required>
                    <input type="submit" id="find" name="find" value="Buscar 🔎" class="form-control">
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if (count($rows) == 0) {
    echo ('<h4> ¡No existen registros! </h4>');
} else {
?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">HISTORIAL DE GRUPOS</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>GRUPO</th>
                        <th>ESTADO</th>
                        <th>FECPROCESADO</th>
                        <th>USUARIO</th>
                        <th>REF</th>
                        <th>SOLICITUD</th>
                        <th>FECSOLICITUD</th>
                        <th>ORIGEN</th>
                        <th>DESTINO</th>
                        <th>COMENTARIOS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $item) { ?>
                    <tr>
                        <td><?php echo $item->GRUPO ?></td>
                        <td>
                            <?php
                                switch($item->estado) {
                                    case 0: echo "NUEVO"; break;
                                    case 1: echo "CON SCAN PARCIAL"; break;
                                    case 2: echo "SCANEADO TOTAL"; break;
                                    default: echo $item->estado;
                                }
                            ?>
                        </td>
                        <td><?php echo $item->FECPROCESADO ?></td>
                        <td><?php echo $item->USUARIO ?></td>
                        <td><?php echo $item->REF ?></td>
                        <td><?php echo $item->SOLICITUD ?></td>
                        <td><?php echo $item->FECSOLICITUD ?></td>
                        <td><?php echo $item->ORIGEN ?></td>
                        <td><?php echo $item->DESTINO ?></td>
                        <td><?php echo $item->Comments ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
    }

?>

</div>

<?php
include_once "footer.php";
?>
