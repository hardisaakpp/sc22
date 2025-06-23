<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin<>1){ echo ('<h3> ACCESO DENEGADO </h3>'); exit(); }
    include_once "../cx/bd_scs.php";
    $q="SELECT id,Admin,username,realizaConteo,articulosContar from STORECONTROL.dbo.users u";
    $r=resp_simdim($q);     
?>

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

    <div class="content">
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($r as $f){ ?>
                                <tr>
                                    <td><?php echo $f['username'] ?></td>
                                    <td><?php echo $f['Admin'] ?></td>
                                    <td><?php echo $f['realizaConteo'] ?></td>
                                    <td><?php echo $f['articulosContar'] ?></td>
                                    <th>
                                        <button type="button" class="btn btn-outline-success" onclick="window.location.href='reg_modif.php?idcab=<?php echo $f['id'] ?>'"> ✏️ </button>   
                                        <button type="button" class="btn btn-outline-success" onclick="window.location.href='php/delete_user.php?idcab=<?php echo $f['id'] ?>'"> ❌ </button>
                                    </th>                            
                                </tr>                      
                            <?php } ?>   
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
      
<?php include_once "footer.php"; ?>