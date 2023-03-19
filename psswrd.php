<?php
    include_once "header.php";
?>


<!-- Breadcrumbs-->
   <!-- <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>CAMBIO DE CLAVE</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li class="active">Widgets</li>
                            </ol>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>-->
<!-- /.breadcrumbs-->
 
<div class="content">
<!-- Content -->
 
<script>
    function mostrar() {
    var p1 = document.getElementById("pass1");
    var p2 = document.getElementById("pass2");
    var p3 = document.getElementById("pass3");
    if (p1.type === "password") {
        p1.type = "text";
        p2.type = "text";
        p3.type = "text";
    } else {
        p1.type = "password";
        p2.type = "password";
        p3.type = "password";
    }
    }

    function prepareCheckPass(){
        var p1 = document.getElementById("pass2");
        var p2 = document.getElementById("pass3");
        if (p1.value.trim()=='' ||  p2.value.trim()=='') {
            // alert('Verifique nueva clave.');
            Swal.fire({
                //position: 'top-end',
                icon: 'error',
                title: 'Llene todos los campos',
                showConfirmButton: false,
                timer: 1500
                })
        } else {
            if (p1.value==p2.value) {
                const oldPas = document.getElementById("pass1");
                checkPass(oldPas.value, p1.value);    
            } else {
            // alert('Verifique nueva clave.');
                Swal.fire({
                //position: 'top-end',
                icon: 'error',
                title: 'Verifique nueva clave, no coincide',
                showConfirmButton: false,
                timer: 1500
                })
            }
        } 
    }

    function checkPass(passOld, passNew) {
    
        var parametros = 
            {
                "userId" : '<?php echo $userId; ?>' ,
                "oldPass" : passOld ,
                "newPass" : passNew
            };

            $.ajax({
                data: parametros,
                url: 'php/checkPass.php',
                type: 'POST',
            //    timeout: 3000,
                success: function(data){
                //$("#find").click();
                    if (data==1) {
                        Swal.fire({
                        icon: 'success',
                        title: '👌😀',
                        text: 'Clave actualizada correctamente!'
                        })
                    } else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'La clave actual es incorrecta!'
                        })
                    }
                },
                error: function(){
                    console.log('error de conexion - revisa tu red');
                }
            });
    }
</script>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Cambio de Clave</div>
            <div class="card-body card-block">
                <form action="#" method="post" class="">
                    
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-key"></i></div>
                            <input type="password" id="pass1" name="pass1" placeholder="Clave actual" class="form-control" required>
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-asterisk"></i></div>
                            <input type="password" id="pass2" name="pass2" placeholder="Nueva clave" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-asterisk"></i></div>
                            <input type="password" id="pass3" name="pass3" placeholder="Confirmar clave" class="form-control" required>
                        </div>
                    </div>
                    <div style="float:right;">
                        <label class="switch switch-3d switch-primary mr-3">Mostrar <input type="checkbox" class="switch-input" onclick="mostrar()">  <span class="switch-label"></span> <span class="switch-handle"></span></label>
                    </div>
                    <div class="form-actions form-group">
                        <button type="button" class="btn btn-outline-success btn-sm" onClick="prepareCheckPass()">Confirmar</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onClick="window.location.href='wllcm.php'">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    
<!-- FIN Content -->
</div>
      
<?php
    include_once "footer.php";
?>

