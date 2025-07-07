<?php
    include_once "header.php";
    $id = $_GET["idcab"];
    include_once "./php/bd_StoreControl.php";
    $sentencia = $db->prepare("SELECT *
     FROM users WHERE id = ?;");
    $sentencia->execute([$id]);
    $usuarios = $sentencia->fetchObject();
    if (!$usuarios) {
        #No existe
        echo "¡No existe usuario con ese ID!";
        exit();
    }

        $s1 = $db->query("select * from Almacen" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
       
        $s2 = $db->query("SELECT username FROM users" );
        $userss = $s2->fetchAll(PDO::FETCH_OBJ); 
?>

        
<!-- Breadcrumbs-->
<div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>EDICIÓN DE USUARIOS</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li>
                                <button type="button" class="btn btn-outline-danger" onclick="window.location.href='userL.php'">Cancelar</button>
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
     
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                <strong>Editar</strong> Usuario
            </div>
            <div class="card-body card-block">
                <form action="./php/update_user.php?idcab=<?php echo $usuarios->id ?>"  method="POST" class="">
                <input type="hidden" name="id" value="<?php echo $usuarios->id; ?>">
                    <div class="form-group">
                        <label for="username" class=" form-control-label">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $usuarios->username; ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class=" form-control-label">Password</label>
                        <input type="password" id="password" value = "<?php echo $usuarios->password ?>" name="password" placeholder="Enter Password.." class="form-control" required>
                    </div>
                    <div class="row form-group">
                        <div class="col col-md-3"><label class=" form-control-label">Perfil: </label></div>
                        <div class="col col-md-9">
                            <div class="form-check">
                            <div class="radio">
                            <label for="inline-radio1" class="form-check-label">
                <input type="radio" id="padmin" name="radios" value="1" class="form-check-input" <?php echo ($usuarios->perfil == 1) ? 'checked' : ''; ?>> Administrador
            </label>
                            </div>
                            <div class="radio">
                            <label for="inline-radio2" class="form-check-label">
                <input type="radio" id="ptienda" name="radios" value="2" class="form-check-input" <?php echo ($usuarios->perfil == 2) ? 'checked' : ''; ?>> Tienda
            </label>
                            </div>
                            <div class="radio">
                            <label for="inline-radio3" class="form-check-label">
                <input type="radio" id="pasisinvs" name="radios" value="4" class="form-check-input" <?php echo ($usuarios->perfil == 4) ? 'checked' : ''; ?>> Asis.Toma Fisicas Totales
            </label>
                            </div>
                            <div class="radio">
                            <label for="inline-radio3" class="form-check-label">
                <input type="radio" id="pbodega" name="radios" value="5" class="form-check-input" <?php echo ($usuarios->perfil == 5) ? 'checked' : ''; ?>> Bodega
            </label>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <div class="checkbox">
                            <label for="checkbox2" class="form-check-label ">
                            <input type="checkbox" id="conteo" name="conteo" <?php echo ($usuarios->realizaConteo == "1") ? 'checked' : ''; ?> class="form-check-input"> Realiza Conteo Aleatorio
                            </label>
                        </div>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="items" class=" form-control-label">Items a contar</label>
                        <input type="number" id="items" name="items" placeholder="Cantidad de articulos.." class="form-control" value="<?php echo $usuarios->articulosContar; ?>" required>
                    </div>
                    <div class="form-group">
                            <label for="turno"  class=" form-control-label" class="standardSelect">Almacen para Cierre de Caja</label>
                            <select name='whsCierre'  data-placeholder='Selecciona el almacen'  class='js-example-basic-single js-states form-control' id='whsCierre'  Size='Number_of_options' required>
                            <option value="0">Ninguno</option>
                            <?php foreach($whs as $wh) { ?>
                                <option value="<?php echo $wh->id ?>" <?php if ($wh->id == $usuarios->fk_ID_almacen_cierre) echo 'selected' ?>>
                                    <?php echo $wh->cod_almacen . ' ' . $wh->nombre ?>
                                </option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsInvs" class=" form-control-label" class="standardSelect">Almacen para Inventarios</label>
                            <select name='whsInvs'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsInvs'  Size='Number_of_options'>
                            <option value="0">Ninguno</option>
                            <?php foreach($whs as $wh) { ?>
                                <option value="<?php echo $wh->id ?>" <?php if ($wh->id == $usuarios->fk_ID_almacen_invs) echo 'selected' ?>>
                                    <?php echo $wh->cod_almacen . ' ' . $wh->nombre ?>
                                </option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsBodega" class=" form-control-label" class="standardSelect">Almacen para Bodega</label>
                            <select name='whsBodega'  data-placeholder='Selecciona el almacen' class='js-example-basic-single form-control' id='whsBodega'  Size='Number_of_options'>
                            <option value="0">Ninguno</option>
                            <?php foreach($whs as $wh) { ?>
                                <option value="<?php echo $wh->id ?>" <?php if ($wh->id == $usuarios->fk_ID_almacen_bodeg) echo 'selected' ?>>
                                    <?php echo $wh->cod_almacen . ' ' . $wh->nombre ?>
                                </option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsTransitorio" class=" form-control-label" class="standardSelect">Almacen Transitorio asociado</label>
                            <select name='whsTransitorio'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsTransitorio'  Size='Number_of_options'>
                            <option value="0">Ninguno</option>
                            <?php foreach($whs as $wh) { ?>
                                <option value="<?php echo $wh->id ?>" <?php if ($wh->id == $usuarios->fk_ID_almacen_transitorio) echo 'selected' ?>>
                                    <?php echo $wh->cod_almacen . ' ' . $wh->nombre ?>
                                </option>
                            <?php } ?>
                            </select>
                    </div>
    
                    <div class="form-group">
                        <label for="codTimeSoft" class=" form-control-label">Cod. Centro de Costos TimeSoft</label>
                        <input type="number" id="codTimeSoft" name="codTimeSoft" placeholder="Enter code" class="form-control" value="<?php echo $usuarios->Timesoft_CentroCosto ?>" required>
                    </div>
        
                  

                    <div class="form-group">
                        <label for="Email1" class="px-1  form-control-label">Email tienda</label>
                        <input type="email" id="Email1" name="Email1" placeholder="jane.doe@example.com" class="form-control" value="<?php echo $usuarios->email ?>">
                    </div>
                    <div class="form-group">
                        <label for="Email2" class="px-1  form-control-label">Email supervisor</label>
                        <input type="email" id="Email2" name="Email2" placeholder="jane.doe@example.com" class="form-control" value="<?php echo $usuarios->emailSuper ?>">
                    </div>


            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> Submit
                </button>
                <button type="reset" class="btn btn-danger btn-sm">
                    <i class="fa fa-ban"></i> Reset
                </button>
            </div>
            </form>
        </div>

   


      
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
<?php    include_once "footer.php"; ?>