<?php 
     session_start();
     if(isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] != '' && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){ header("location:wllcm.php"); exit(); }
     if(!isset($_SESSION['login_attempts'])){ $_SESSION['login_attempts']=0; $_SESSION['last_attempt']=time(); }
     if((time()-$_SESSION['last_attempt'])>180){ $_SESSION['login_attempts']=0; $_SESSION['last_attempt']=time(); }
     if($_SESSION['login_attempts']>= 5){ 
          if ($_SESSION['login_attempts'] >= 5){
               $remaining = 180 - (time() - $_SESSION['last_attempt']);
               die('<!DOCTYPE html>
                    <html lang="es">
                         <head>
                              <meta charset="UTF-8">
                              <title>Acceso bloqueado</title>
                              <meta name="viewport" content="width=device-width, initial-scale=1">
                              <!-- Bootstrap 5.3 Dark -->
                              <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                              <style>
                                   body {
                                        background-color: #121212;
                                        color: #f8f9fa;
                                   }
                                   .card-dark {
                                        background-color: #1f1f1f;
                                        border: 1px solid #333;
                                        border-radius: 10px;
                                        box-shadow: 0 0 10px rgba(255,255,255,0.05);
                                   }
                                   .justified-text {
                                        text-align: justify;
                                   }
                                   #countdown {
                                        font-size: 1.7rem;
                                        font-weight: bold;
                                        color: #ffc107;
                                   }
                              </style>
                         </head>
                         <body>

                              <div class="container my-5">
                              <div class="row justify-content-center">
                                   <div class="col-md-8 col-lg-6">
                                        <div class="card card-dark p-4">
                                             <div class="card-body text-light">
                                                  <h4 class="text-center mb-3">Acceso temporalmente bloqueado</h4>
                                                  <p class="justified-text">
                                                  Se han registrado múltiples intentos fallidos de inicio de sesión. Por motivos de seguridad, el acceso ha sido restringido temporalmente. 
                                                  Por favor, espere <span id="countdown">03:00</span> antes de intentar nuevamente.
                                                  </p>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              </div>

                              <!-- Countdown Script -->
                              <script>
                              let remaining = 180;
                              function formatTime(s) {
                                   const m = Math.floor(s / 60).toString().padStart(2, "0");
                                   const sec = (s % 60).toString().padStart(2, "0");
                                   return m + ":" + sec;
                              }
                              const countdownEl = document.getElementById("countdown");
                              const interval = setInterval(() => {
                                   if (remaining <= 0) {
                                        clearInterval(interval);
                                        location.reload(); // Reload login page
                                   } else {
                                        countdownEl.textContent = formatTime(--remaining);
                                   }
                              }, 1000);
                              </script>

                         </body>
                    </html>'
               );
          }
     }

     try{            
          if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["login"])){
               $us = trim($_POST['iusername'] ?? '');
               $ps = $_POST['ipassword'] ?? '';
               if(empty($us) || empty($ps)){ session_unset(); session_destroy(); $message = '<label>Los campos son requeridos</label>'; }  
               else{
                    include_once "../../cx/bd_scs.php";
                    $message = '';
                    $q="SELECT count(*) FROM STORECONTROL.dbo.users WHERE username = '$us' AND password = '$ps'";
                    $r=resp_oneval($q);
                    if($r==0){ $_SESSION['login_attempts'] += 1; $_SESSION['last_attempt'] = time(); $message = "<label>Credenciales Incorrectas, Número de Intentos $_SESSION[login_attempts] de 5</label>"; }
                    else{ 
                         $q="SELECT id,fk_ID_almacen_cierre,fk_ID_almacen_invs,fk_ID_almacen_turemp,perfil FROM STORECONTROL.dbo.users WHERE username = '$us' AND password = '$ps'";
                         $r=resp_onedim($q);
                         session_regenerate_id(true);                     
                         $_SESSION["idU"]=$r['id'];                          
                         $_SESSION["whsCica"]=$r['fk_ID_almacen_cierre'];  
                         $_SESSION["whsInvs"]=$r['fk_ID_almacen_invs'];                       
                         $_SESSION["whsTurem"]=$r['fk_ID_almacen_turemp'];                  
                         $_SESSION["perfil"]=$r['perfil']; 
                         $_SESSION["username"]=$us;
                         $_SESSION['logged_in'] = TRUE;
                         if(empty($_SESSION['csrf_token'])){ $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
                         header("location:../wllcm.php");
                         exit();
                    }
               }  
          }else{ session_unset(); session_destroy(); }  
     }catch(PDOException $error){ $message = $error->getMessage(); }  
     include_once('header_ix.php');
?>   
     <div class="container">
          <div class="row justify-content-center">
               <div class="col-md-6 col-lg-5">
                    <div class="card login-card">
                         <div class="card-body p-3" align="center">  
                              <h2 class="fw-bold mb-2 text-uppercase">AUTENTICACIÓN</h2>          
                              <small class="text-muted">STORE CONTROL</small><br><br>
                              <form method="post" class="login100-form validate-form">
                                   <div class="wrap-input100 validate-input mb-3"  data-validate="Enter username"> 
                                        <label for="iusername" class="form-label">Usuario</label>     
                                        <input type="text" class="form-control" name="iusername" id="iusername" pattern="[A-Za-z0-9\-]+" maxlength="20" onkeyup=" var start = this.selectionStart; var end = this.selectionEnd; this.value = this.value.toUpperCase(); this.setSelectionRange(start, end);" class="input100" required/>  
                                        <span class="focus-input100" data-placeholder="Usuario"></span>
                                   </div>                 
                                   <div class="wrap-input100 validate-input mb-3" data-validate="Enter password">
                                        <label for="ipassword" class="form-label">Contraseña</label>
                                        <span class="btn-show-pass"><i class="zmdi zmdi-eye"></i></span>
                                        <input type="password" name="ipassword" class="input100 form-control" maxlength="20" required/>  
                                        <span class="focus-input100" data-placeholder="Contraseña"></span>
                                   </div>
                                   <?php if(isset($message)){ echo '<div class="alert alert-danger text-center">'.$message.'</div>'; } ?> 
                                   <div class="container-login100-form-btn">
                                        <div class="wrap-login100-form-btn d-grid">
                                             <div class="login100-form-bgbtn"></div>
                                             <button class="login100-form-btn btn-gradient" type="submit" name="login">Ingresar</button>
                                        </div>
                                   </div>
                              </form>  
                              <div class="text-center mt-4" align="justify">
                                   <small class="text-muted">¿Primer ingreso? Usa tu usuario como contraseña.</small><br>
                                   <small class="text-muted">¿Problemas? <a href="mailto:sistemas@sunsetcorpholding.com">Contáctanos</a></small>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>

<?php include_once('footer_ix.php'); ?>