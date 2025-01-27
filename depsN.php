<?php
    include_once "header.php";
    //si no es admin no abre
    if($whsCica=0){
        echo ('<h3> NO TIENE TIENDA ASIGNADA </h3>');
        //exit();
    }else{

        $s1 = $db->query("select top 1 * from Almacen WHERE id=$whsCica" );
        $almacen = $s1->fetchObject();       
       
        echo $whsCica;
        echo  $almacen->nombre;
?>


<div class="content">
<!---------------------------------------------->
<!----------------- Content -------------------->
<!---------------------------------------------->



     
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                <strong>Nuevo</strong> Usuario
            </div>
            <div class="card-body card-block">
                <form action="php/insert_user.php" id='frmUser' method="post" class="">
                    <div class="form-group">
                        <label for="Username" class=" form-control-label">Username</label>
                        <input type="text" id="Username" name="Username" placeholder="Enter username.." class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class=" form-control-label">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter Password.." class="form-control" required>
                    </div>
                    <div class="row form-group">
                        <div class="col col-md-3"><label class=" form-control-label">Perfil: </label></div>
                        <div class="col col-md-9">
                            <div class="form-check">
                            <div class="radio">
                                <label for="inline-radio1" class="form-check-label ">
                                    <input type="radio" id="padmin" name="radios" value="1" class="form-check-input" checked>Administrador 
                                </label>
                            </div>
                            <div class="radio">
                                <label for="inline-radio2" class="form-check-label ">
                                    <input type="radio" id="ptienda" name="radios" value="2" class="form-check-input">Tienda
                                </label>
                            </div>
                            <div class="radio">
                                <label for="inline-radio3" class="form-check-label ">
                                    <input type="radio" id="pasisinvs" name="radios" value="4" class="form-check-input">Asis.Toma Fisicas Totales
                                </label>
                            </div>
                            <div class="radio">
                                <label for="inline-radio3" class="form-check-label ">
                                    <input type="radio" id="pbodega" name="radios" value="5" class="form-check-input">Bodega
                                </label>
                            </div>
                            </div>
                        </div>
                    </div>



                    <div class="form-check">
                        <!--<div class="checkbox">
                            <label for="checkbox1" class="form-check-label ">
                                <input type="checkbox" id="admin" name="admin" value="0" class="form-check-input">Administrador
                            </label>
                        </div>-->
                        <div class="checkbox">
                            <label for="checkbox2" class="form-check-label ">
                                <input type="checkbox" id="conteo" name="conteo" value="0" class="form-check-input"> Realiza Conteo Aleatorio
                            </label>
                        </div>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="items" class=" form-control-label">Items a contar</label>
                        <input type="number" id="items" name="items" placeholder="Cantidad de articulos.." class="form-control" value=0 required>
                    </div>
                    <div class="form-group">
                            <label for="turno"  class=" form-control-label" class="standardSelect">Almacen para Cierre de Caja</label>
                            <select name='whsCierre'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsCierre'  Size='Number_of_options' required>
                                <option value="0">Ninguno</option>
                                <?php   foreach($whs as $wh){ ?>
                                    <option value="<?php echo $wh->id ?>"><?php echo $wh->cod_almacen . ' '.$wh->nombre  ?></option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsInvs" class=" form-control-label" class="standardSelect">Almacen para Inventarios</label>
                            <select name='whsInvs'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsInvs'  Size='Number_of_options'>
                                 <option value="0">Ninguno</option>
                                <?php   foreach($whs as $wh){ ?>
                                    <option value="<?php echo $wh->id ?>"><?php echo $wh->cod_almacen . ' '.$wh->nombre  ?></option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsHorario" class=" form-control-label" class="standardSelect">Almacen para Horarios de Personal</label>
                            <select name='whsHorario'  data-placeholder='Selecciona el almacen' class='js-example-basic-single form-control' id='whsHorario'  Size='Number_of_options'>
                            <option value="0">Ninguno</option>
                                <?php   foreach($whs as $wh){ ?>
                                    <option value="<?php echo $wh->id ?>"><?php echo $wh->cod_almacen . ' '.$wh->nombre  ?></option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="form-group">
                    <label for="whsTransitorio" class=" form-control-label" class="standardSelect">Almacen Transitorio asociado</label>
                            <select name='whsTransitorio'  data-placeholder='Selecciona el almacen' class='js-example-basic-single js-states form-control' id='whsTransitorio'  Size='Number_of_options'>
                            <option value="0">Ninguno</option>
                                <?php   foreach($whs as $wh){ ?>
                                    <option value="<?php echo $wh->id ?>"><?php echo $wh->cod_almacen . ' '.$wh->nombre  ?></option>
                                <?php } ?>
                            </select>
                    </div>
    
                    <div class="form-group">
                        <label for="codTimeSoft" class=" form-control-label">Cod. Centro de Costos TimeSoft</label>
                        <input type="number" id="codTimeSoft" name="codTimeSoft" placeholder="Enter code" class="form-control" value='0' required>
                    </div>
        
                  

                    <div class="form-group">
                        <label for="Email1" class="px-1  form-control-label">Email tienda</label>
                        <input type="email" id="Email1" name="Email1" placeholder="jane.doe@example.com" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="Email2" class="px-1  form-control-label">Email supervisor</label>
                        <input type="email" id="Email2" name="Email2" placeholder="jane.doe@example.com" class="form-control">
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

   


<script>
var aux=0;

document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("frmUser").addEventListener('submit', validarFormulario); 
});

function validarFormulario(evento) {
  
    evento.preventDefault();
    

  var usuario = document.getElementById('Username').value;


  <?php   foreach($userss as $user){ ?>
        if ( '<?php  echo $user->username ?>' == usuario ) {
            Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ya esta en uso el username!'
                    })
                    return;
        }            
    <?php  } ?>

 this.submit();
}


</script>

      
<!---------------------------------------------->
<!--------------Fin Content -------------------->
<!---------------------------------------------->
</div>
<?php  }; ?>
      
<?php    include_once "footer.php"; ?>