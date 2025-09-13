<?php
    include_once "header.php";

    if (!isset($_GET["idcab"])) {
        exit("Falta parámetro idcab");
    }
    $idcab = intval($_GET["idcab"]);

    // Pendientes (estado <> 2)
    $s1 = $db->query("
        SELECT g.[id]
            ,g.[fk_idgroup]
            ,g.[estado]
            ,g.[fk_docnumsotcab]
            ,g.Filler
            ,g.ToWhsCode
            ,g.DocDate
        FROM [dbo].[ced_groupsot] g 
        WHERE [fk_idgroup] = $idcab AND estado <> 2
    ");
    $users = $s1->fetchAll(PDO::FETCH_OBJ);

    // Terminados (estado = 2)
    $s2 = $db->query("
        SELECT g.[id]
            ,g.[fk_idgroup]
            ,g.[estado]
            ,g.[fk_docnumsotcab]
            ,g.Filler
            ,g.ToWhsCode
            ,g.DocDate
        FROM [dbo].[ced_groupsot] g 
        WHERE [fk_idgroup] = $idcab AND estado = 2
    ");
    $users2 = $s2->fetchAll(PDO::FETCH_OBJ);  
?>
<!-- Breadcrumbs-->
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Solicitudes de Lista <?php echo $idcab; ?></h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li></li>
                        </ol>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
<!-- /.breadcrumbs-->

<div class="content">
    <div class="row">
        <?php foreach($users as $user){ ?>
            <?php if ($user->estado == 1) { ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card" 
                         data-docnum="<?php echo $user->fk_docnumsotcab; ?>" 
                         data-id="<?php echo $user->id; ?>">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-1">
                                    <a href="cediPickT.php?idcab=<?php echo $user->id?>">
                                        <i class="pe-7s-config"></i>
                                    </a>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text">#<span class="count"><?php echo $user->fk_docnumsotcab ?></span></div>
                                        <div class="stat-text"><?php echo '->'.$user->ToWhsCode?></div>
                                        <div class="stat-heading"><?php echo $user->DocDate ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card" 
                         data-docnum="<?php echo $user->fk_docnumsotcab; ?>" 
                         data-id="<?php echo $user->id; ?>">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-4">
                                    <a href="cediPickT.php?idcab=<?php echo $user->id?>">
                                        <i class="pe-7s-box2"></i>
                                    </a>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text">#<span class="count"><?php echo $user->fk_docnumsotcab ?></span></div>
                                        <div class="stat-text"><?php echo '->'.$user->ToWhsCode?></div>
                                        <div class="stat-heading"><?php echo $user->DocDate ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?> 
    </div>

    <div class="row">
        <?php foreach($users2 as $user){ ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-flat-color-1" 
                     data-docnum="<?php echo $user->fk_docnumsotcab; ?>" 
                     data-id="<?php echo $user->id; ?>">
                    <div class="card-body">
                        <div class="card-left pt-1 float-left">
                            <h3 class="mb-0 fw-r">
                                <span class="currency float-left mr-1">#</span>
                                <span class="count"><?php echo $user->fk_docnumsotcab?></span>
                            </h3>
                            <p class="text-light mt-1 m-0"><?php echo $user->DocDate .' para '.$user->ToWhsCode ?></p>
                        </div>
                        <div class="card-right float-right text-right">
                            <a href="cediPickT.php?idcab=<?php echo $user->id?>">
                                <i class="icon fade-5 icon-lg pe-7s-check"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?> 
    </div>

    <div class="text-center">
        <a href="cediGrpLdis.php" class="btn btn-secondary mt-2">⬅️ Volver</a>
    </div>
</div>

<!-- Menú contextual personalizado -->
<ul id="contextMenu" class="dropdown-menu" style="display:none; position:absolute; z-index:1000;">
  <li><a href="#" id="descargarDocSap">⬇️ Descargar DocSap</a></li>
  <li><a href="#" id="confirmarRecepcion">✅ Confirmar recepción 100%</a></li>
</ul>

<script>
// Variables globales
let selectedDocNum = null;
let selectedId = null;

// Detectar clic derecho en card
document.querySelectorAll('.card').forEach(function(card) {
    card.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        selectedDocNum = this.getAttribute('data-docnum');
        selectedId     = this.getAttribute('data-id');
        let menu = document.getElementById('contextMenu');
        menu.style.left = e.pageX + 'px';
        menu.style.top = e.pageY + 'px';
        menu.style.display = 'block';
    });
});

// Ocultar menú
document.addEventListener('click', function() {
    document.getElementById('contextMenu').style.display = 'none';
});

// Acción Descargar
document.getElementById('descargarDocSap').addEventListener('click', function(e) {
    e.preventDefault();
    if (selectedDocNum) {
        window.location.href = "descargarDocSap.php?idcab=<?php echo $idcab; ?>&docnum=" + selectedDocNum;
    }
});

// Acción Confirmar Recepción
document.getElementById('confirmarRecepcion').addEventListener('click', function(e) {
    e.preventDefault();
    if (selectedDocNum && selectedId) {
        window.location.href = "confirmarRecepcion.php?idcab=<?php echo $idcab; ?>&docnum=" + selectedDocNum + "&id=" + selectedId;
    }
});
</script>

<?php include_once "footer.php"; ?>
