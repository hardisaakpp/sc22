<?php
    include_once "header.php";

        if (!isset($_GET["idcab"])) {
            exit();
        }
        $idcab = $_GET["idcab"];

    //si no es admin no abre
        $s1 = $db->query("
        SELECT g.[id]
            ,g.[fk_idgroup]
            ,g.[estado]
            ,g.[fk_docnumsotcab]

            ,c.ToWhsCode
            ,c.DocDate
        FROM [dbo].[ced_groupsot] g 
            join SotCab_MT c on g.fk_docnumsotcab=c.DocNum
        where [fk_idgroup]=".$idcab." and estado<>2
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ);  
        $s2 = $db->query("
        SELECT g.[id]
            ,g.[fk_idgroup]
            ,g.[estado]
            ,g.[fk_docnumsotcab]

            ,c.ToWhsCode
            ,c.DocDate
        FROM [dbo].[ced_groupsot] g 
            join SotCab_MT c on g.fk_docnumsotcab=c.DocNum
        where [fk_idgroup]=".$idcab." and estado=2
        " );
        $users2 = $s2->fetchAll(PDO::FETCH_OBJ);  
?>
<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Solicitudes de Grupo <?php echo $idcab; ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
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
<!----------------- Content -------------------->
<!-- Widgets  -->
    <div class="row">

     <?php   
    foreach($users as $user){ 
        //0 inicio, 1 en proceso, 2 terminado

  if ($user->estado==1) {
     ?> 
            <div class="col-lg-3 col-md-6">
                <div class="card">
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
                                    <div class="stat-text"><?php echo 'Destino:'.$user->ToWhsCode?></div>
                                    <div class="stat-heading"><?php echo $user->DocDate ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
     <?php  
} else { #es igual a 0
     ?> 
   <div class="col-lg-3 col-md-6">
                <div class="card">
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
                                    <div class="stat-text"><?php echo 'Destino:'.$user->ToWhsCode?></div>
                                    <div class="stat-heading"><?php echo $user->DocDate ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
     <?php  
}
        ?> 
    <?php } ?> 

    </div>
 <div class="row">
        <?php   
        foreach($users2 as $user){ 
            ?> 
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-flat-color-1">
                            <div class="card-body">
                                <div class="card-left pt-1 float-left">
                                    <h3 class="mb-0 fw-r">
                                        <span class="currency float-left mr-1">#</span>
                                        <span class="count"><?php echo $user->fk_docnumsotcab?></span>
                                    </h3>
                                    <p class="text-light mt-1 m-0"><?php echo $user->DocDate .' para '.$user->ToWhsCode ?></p>
                                </div><!-- /.card-left -->

                                <div class="card-right float-right text-right">
                                    <a href="cediPickT.php?idcab=<?php echo $user->id?>">
                                    <i class="icon fade-5 icon-lg pe-7s-check"></i>
                                    </a>
                                </div><!-- /.card-right -->

                            </div>

                        </div>
                    </div>
        <?php } ?> 
    </div>
</div>
<?php   
include_once "footer.php";
 ?>