<?php
    include_once "header.php";
    //si no es admin no abre
        $s1 = $db->query("
        select * from ced_group where estado = 1
        " );
        $users = $s1->fetchAll(PDO::FETCH_OBJ);   
?>

<!-- Breadcrumbs-->
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>GRUPOS PARA RECOLECCION</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-warning" onclick="location.reload();">F5</button>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='wllcm.php'">X</button>
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
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->


<!-- Widgets  -->
    <div class="row">

    <?php   foreach($users as $user){ ?>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-3">
                        <a href="cediGrpLdid.php?idcab=<?php echo $user->id?>">
                            <i class="pe-7s-browser"></i>
                            </a>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count"><?php echo $user->id ?></span> #</div>
                                <div class="stat-text"><?php echo 'Conteo N°.'.$user->id?></div>
                                <div class="stat-heading"><?php echo $user->comentario .' al '.$user->fecha_creacion ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?> 

        

    </div>


                 
               


<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
      
<?php   
include_once "footer.php";
 ?>