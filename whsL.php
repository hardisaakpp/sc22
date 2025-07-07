<?php
    include_once "header.php";
    //si no es admin no abre
    if($userAdmin==0){
        echo ('ACCESO DENEGADO');
        exit();
        }

        $s1 = $db->query("select * from Almacen" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
       
?>
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
                        <th>Id [SC]</th>
                        <th>Empresa</th>
                        <th>Cod.Almacen</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                <?php   foreach($whs as $wh){ ?>
                    <tr>
                        <td><?php echo $wh->id ?></td>
                        <td><?php echo $wh->fk_emp ?></td>
                        <td><?php echo $wh->cod_almacen ?></td>
                        <td><?php echo $wh->nombre ?></td>
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
      
<?php    include_once "footer.php"; ?>