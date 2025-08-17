<?php
    include_once "header.php";
    
    include_once "./php/bd_StoreControl.php";


        $sentencia = $db->prepare("SELECT * FROM users WHERE id = ?;");
        $sentencia->execute([$userId]);
        $usuarios = $sentencia->fetchObject();

        $s1 = $db->query("select * from Almacen" );
        $whs = $s1->fetchAll(PDO::FETCH_OBJ);       
   
?>

<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->
     
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                 <strong>Mi perfil</strong>
            </div>
            <div class="card-body card-block">
                <form action="./php/update_user.php?idcab=<?php echo $usuarios->id ?>"  method="POST" class="">
                <input type="hidden" name="id" value="<?php echo $usuarios->id; ?>">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Username</label></div>
                            <div class="col-12 col-md-9"><input type="text" id="username" name="username" value="<?php echo $usuarios->username; ?>" class="col-12 col-md-9" disabled></div>
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
                    <div class="row form-group">
                      
                            <label for="checkbox2" class="form-check-label ">
                                <?php 
                                    if ($usuarios->realizaConteo == "1") {
                                        echo '*   Si realiza conteo aleatorio de '.$usuarios->articulosContar.' articulos';
                                    } else {
                                        echo '*   No realiza conteo aleatorio de articulos';
                                    }
                                ?>
                            </label>
                       
                    </div>
           
                   
                    <div class="form-group">
                            <label for="turno"  class=" form-control-label" class="standardSelect">Almacen para Cierre de Caja</label>
                            <select name='whsCierre'  data-placeholder='Selecciona el almacen'  class='js-example-basic-single js-states form-control' id='whsCierre'  Size='Number_of_options' disabled>
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
                            <select name='whsInvs'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsInvs'  Size='Number_of_options' disabled>
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
                            <select name='whsBodega'  data-placeholder='Selecciona el almacen' class='js-example-basic-single form-control' id='whsBodega'  Size='Number_of_options' disabled>
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
                            <select name='whsTransitorio'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsTransitorio'  Size='Number_of_options' disabled>
                            <option value="0">Ninguno</option>
                            <?php foreach($whs as $wh) { ?>
                                <option value="<?php echo $wh->id ?>" <?php if ($wh->id == $usuarios->fk_ID_almacen_transitorio) echo 'selected' ?>>
                                    <?php echo $wh->cod_almacen . ' ' . $wh->nombre ?>
                                </option>
                            <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsCD" class=" form-control-label" class="standardSelect">Almacen CD para pedidos</label>
                            <select name='whsCD'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsCD'  Size='Number_of_options' disabled>
                            <option value="0">Ninguno</option>
                            <?php foreach($whs as $wh) { ?>
                                <option value="<?php echo $wh->id ?>" <?php if ($wh->id == $usuarios->fk_ID_almacen_CD) echo 'selected' ?>>
                                    <?php echo $wh->cod_almacen . ' ' . $wh->nombre ?>
                                </option>
                            <?php } ?>
                            </select>
                    </div>
    

                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label for="text-input" class=" form-control-label">Email tienda</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="email" id="Email1" name="Email1" placeholder=" - " class="form-control" value="<?php echo $usuarios->email ?>" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label for="text-input" class=" form-control-label">Email supervisor</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="email" id="Email2" name="Email2" placeholder=" - " class="form-control" value="<?php echo $usuarios->emailSuper ?>" disabled>
                            </div>
                        </div>



            </div>
       
            </form>
        </div>
</div>
   


      
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
<?php    include_once "footer.php"; ?>