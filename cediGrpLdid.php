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
                        <h1 id="tituloGrupo">Solicitudes de Lista <?php echo $idcab; ?></h1>
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

<!-- Menú contextual para las cards -->
<ul id="contextMenuCard" class="dropdown-menu" style="display:none; position:absolute; z-index:10000;">
  <li><a href="#" id="descargarDocSap">⬇️ Descargar DocSap</a></li>
  <li><a href="#" id="confirmarRecepcion">✅ Confirmar recepción 100%</a></li>
</ul>

<!-- Menú contextual para el título (descargar todo el grupo) -->
<ul id="contextMenuTitulo" class="dropdown-menu" style="display:none; position:absolute; z-index:10000;">
  <li><a href="#" id="descargarGrupo">⬇️ Descargar Excel Grupo</a></li>
  <li><a href="#" id="confirmarRecepcionGrupo">✅ Confirmar recepción 100% Grupo</a></li>
  <li><a href="#" id="consultarTransferencias">🔎 Consultar num.transferencias</a></li>
</ul>

<script>
// util: ocultar todos los menus
function hideAllContextMenus() {
    const menus = document.querySelectorAll('#contextMenuCard, #contextMenuTitulo');
    menus.forEach(m => m.style.display = 'none');
}

document.getElementById('consultarTransferencias').addEventListener('click', function(e) {
    e.preventDefault();
    hideAllContextMenus();
    window.location.href = "consultarTransferencias.php?idcab=<?php echo $idcab; ?>";
});



let selectedDocNum = null;
let selectedId = null;

// ----- CONTEXT MENU para CARDS -----
document.querySelectorAll('.card').forEach(function(card) {
    card.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        hideAllContextMenus();
        selectedDocNum = this.getAttribute('data-docnum');
        selectedId     = this.getAttribute('data-id');

        const menu = document.getElementById('contextMenuCard');
        menu.style.left = e.pageX + 'px';
        menu.style.top = e.pageY + 'px';
        menu.style.display = 'block';
    });
});

// Opciones del menu de card
document.getElementById('descargarDocSap').addEventListener('click', function(e) {
    e.preventDefault();
    hideAllContextMenus();
    if (!selectedDocNum) { alert('No se identificó el documento.'); return; }
    window.location.href = "descargarDocSap.php?idcab=<?php echo $idcab; ?>&docnum=" + encodeURIComponent(selectedDocNum);
});

document.getElementById('confirmarRecepcion').addEventListener('click', function(e) {
    e.preventDefault();
    hideAllContextMenus();
    if (!selectedDocNum || !selectedId) { alert('Faltan parámetros.'); return; }
    if (!confirm('¿Confirmar recepción 100% para la solicitud ' + selectedDocNum + ' ?')) return;
    window.location.href = "confirmarRecepcion.php?idcab=<?php echo $idcab; ?>&docnum=" + encodeURIComponent(selectedDocNum) + "&id=" + encodeURIComponent(selectedId);
});

// Acción Confirmar Recepción Grupo
document.getElementById('confirmarRecepcionGrupo').addEventListener('click', function(e) {
    e.preventDefault();
    hideAllContextMenus();
    if (!confirm('¿Confirmar recepción 100% de TODA la lista <?php echo $idcab; ?> ?')) return;
    // Llama a confirmarRecepcionGrupo.php y recarga al terminar
    window.location.href = "confirmarRecepcionGrupo.php?idcab=<?php echo $idcab; ?>";
});


// ----- CONTEXT MENU para TITULO -----
const titulo = document.getElementById('tituloGrupo');
titulo.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    hideAllContextMenus();
    const menu = document.getElementById('contextMenuTitulo');
    menu.style.left = e.pageX + 'px';
    menu.style.top = e.pageY + 'px';
    menu.style.display = 'block';
});

document.getElementById('descargarGrupo').addEventListener('click', function(e) {
    e.preventDefault();
    hideAllContextMenus();
    window.location.href = "descargarGrupo.php?idcab=<?php echo $idcab; ?>";
});

// Ocultar menus al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.closest('#contextMenuCard') || e.target.closest('#contextMenuTitulo')) return;
    hideAllContextMenus();
});

// ocultar con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideAllContextMenus();
});
</script>



<?php include_once "footer.php"; ?>
