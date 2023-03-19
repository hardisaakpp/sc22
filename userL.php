<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){
        echo ('<h3> ACCESO DENEGADO </h3>');
        //exit();
        }else{

        $s1 = $db->query("				select id,[Admin],username,realizaConteo,articulosContar,
        (select a.cod_almacen from Almacen a where a.id=u.fk_ID_almacen_cierre) as h1,
        (select a.cod_almacen from Almacen a where a.id=u.fk_ID_almacen_invs) as h2,
        (select a.cod_almacen from Almacen a where a.id=u.fk_ID_almacen_turemp) as h3,
        (select a.cod_almacen from Almacen a where a.id=u.fk_ID_almacen_transitorio) as h4
    from users u 	" );
        $users = $s1->fetchAll(PDO::FETCH_OBJ);       
       
?>

<!-- Breadcrumbs-->
<div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>MANTENIMIENTO DE USUARIOS</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                
                               
                                <button type="button" class="btn btn-outline-success" onclick="window.location.href='userN.php'">Nuevo</button>
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


<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">Usuarios</strong>
        </div>
        <div class="card-body">
            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Administrador</th>
                        <th>Realiza Conteo</th>
                        <th>Articulos</th>
                        <th>CierreCaja</th>
                        <th>Inventarios</th>
                        <th>Horarios</th>
                        <th>Transitorio</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($users as $user){ ?>


                    <tr>
                        <td><?php echo $user->username ?></td>
                        <td><?php echo $user->Admin ?></td>
                        <td><?php echo $user->realizaConteo ?></td>
                        <td><?php echo $user->articulosContar ?></td>
                        <td><?php echo $user->h1 ?></td>
                        <td><?php echo $user->h2 ?></td>
                        <td><?php echo $user->h3 ?></td>
                        <td><?php echo $user->h4 ?></td>

                    </tr>
                   
                <?php } ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>



  
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
<?php  }; ?>
      
<?php    include_once "footer.php"; ?>