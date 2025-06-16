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
            ,g.[activo]
            ,c.ToWhsCode
            ,c.DocDate
        FROM [dbo].[ced_groupsot] g 
            join SotCab_MT c on g.fk_docnumsotcab=c.DocNum
        where [fk_idgroup]=".$idcab."
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
                            <h1>SOLICITUDES</h1>
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
<!----------------- Content -------------------->
<!-- Widgets  -->
    <div class="row">

    <?php   foreach($users as $user){ ?>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-3">
                        <a href="cediPickT.php?idcab=<?php echo $user->id?>">
                            <i class="pe-7s-box2"></i>
                            </a>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text">Sol.<span class="count"><?php echo $user->fk_docnumsotcab ?></span> #</div>
                                <div class="stat-text"><?php echo 'Destino:'.$user->ToWhsCode?></div>
                                <div class="stat-heading"><?php echo $user->DocDate ?></div>
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