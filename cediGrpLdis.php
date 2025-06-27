<?php
    include_once "header.php";

    $desde = isset($_GET['desde']) ? $_GET['desde'] : date('Y-m-d', strtotime('-2 days'));
    $hasta = isset($_GET['hasta']) ? $_GET['hasta'] : date('Y-m-d');
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '5';


if ($estado==5) {
      $s1 = $db->prepare("
        select * from ced_group where (estado<2) and [open]=1
        AND CONVERT(DATE, fecha_creacion) BETWEEN :desde AND :hasta 
        " );
      $s1->execute([':desde' => $desde, ':hasta' => $hasta]);
        $users = $s1->fetchAll(PDO::FETCH_OBJ);   

        $s2 = $db->prepare("
        select * from ced_group where (estado>=2 ) and [open]=1
        AND CONVERT(DATE, fecha_creacion) BETWEEN :desde AND :hasta 
        " );
       $s2->execute([':desde' => $desde, ':hasta' => $hasta]);
        $users2 = $s2->fetchAll(PDO::FETCH_OBJ);   
} else {
      $s1 = $db->prepare("
        select * from ced_group where (estado<2  and estado=:estado) and [open]=1
        AND CONVERT(DATE, fecha_creacion) BETWEEN :desde AND :hasta 
        " );
      $s1->execute([':desde' => $desde, ':hasta' => $hasta, ':estado' => $estado]);
        $users = $s1->fetchAll(PDO::FETCH_OBJ);   

        $s2 = $db->prepare("
        select * from ced_group where (estado>=2 and estado=:estado) and [open]=1
        AND CONVERT(DATE, fecha_creacion) BETWEEN :desde AND :hasta 
        " );
       $s2->execute([':desde' => $desde, ':hasta' => $hasta, ':estado' => $estado]);
        $users2 = $s2->fetchAll(PDO::FETCH_OBJ);  
}

       
?>


    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-12">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Listas de Distribución</h1>
                            <form method="GET" class="form-inline mb-3">

<label for="estado">Estado:</label>
     <select name="estado" id="estado" class="form-control mx-2">
        <option value="5" <?php if($estado === '5') echo 'selected'; ?>>Todos</option>
        <option value="0" <?php if($estado === '0') echo 'selected'; ?>>Nuevo</option>
        <option value="1" <?php if($estado === '1') echo 'selected'; ?>>En proceso</option>
        <option value="2" <?php if($estado === '2') echo 'selected'; ?>>Finalizado</option>
      
  
     </select>

        <label for="desde">Desde:</label>
        <input type="date" name="desde" id="desde" class="form-control mx-2"
               value="<?php echo $desde; ?>">

        <label for="hasta">Hasta:</label>
        <input type="date" name="hasta" id="hasta" class="form-control mx-2"
               value="<?php echo $hasta; ?>">

        <button type="submit" class="btn btn-primary">Buscar</button>



    </form>
                        </div>
                    </div>
                </div>
                 
            </div>
        </div>
    </div>

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->


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
                            <a href="cediGrpLdid.php?idcab=<?php echo $user->id?>">
                                <i class="pe-7s-config"></i>
                                </a>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"># <span class="count"><?php echo $user->id ?></span></div>
                                    <div class="stat-text"><?php echo ''.$user->comentario?></div>
                                    <div class="stat-heading"><?php echo 'Gen.'.$user->fecha_creacion ?></div>
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
                            <a href="cediGrpLdid.php?idcab=<?php echo $user->id?>">
                                <i class="pe-7s-box2"></i>
                                </a>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text">Grupo #<span class="count"><?php echo $user->id ?></span></div>
                                    <div class="stat-text"><?php echo 'Ref.:'.$user->comentario?></div>
                                    <div class="stat-heading"><?php echo $user->fecha_creacion ?></div>
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
                                        <span class="currency float-left mr-1">Grupo #</span>
                                        <span class="count"><?php echo $user->id?></span>
                                    </h3>
                                    <p class="text-light mt-1 m-0"><?php echo $user->comentario .' al '.$user->fecha_creacion ?></p>
                                </div>

                                <div class="card-right float-right text-right">
                                    <a href="cediGrpLdid.php?idcab=<?php echo $user->id?>">
                                    <i class="icon fade-5 icon-lg pe-7s-check"></i>
                                    </a>
                                </div>  

                            </div>

                        </div>
                    </div>

        <?php
  
    
    
    } ?> 
    </div>
                 
</div>
      
<?php   
include_once "footer.php";
 ?>