<?php  
include_once "php/bd_StoreControl.php";
 session_start();  

 if(isset($_SESSION['username']))
  {
     header("location:wllcm.php"); 
  }

 try  
 {  
     
    if(isset($_POST["login"]))  
      {  
           if(empty($_POST["iusername"]) || empty($_POST["ipassword"]))  
           {  
                $message = '<label>Los campos son requeridos</label>';  
           }  
           else  
           {  
               $xusername = $_POST['iusername'];
			$xpassword = $_POST['ipassword'];

               $query = "SELECT * FROM users WHERE username = '" . $xusername . "' AND password = '" . $xpassword . "'";  
              // echo($query);
               $sentencia = $db->query($query);
               $quser = $sentencia->fetchAll(PDO::FETCH_OBJ);


               $sentencia1 = $db->query("SELECT * FROM users WHERE username = '" . $xusername . "' AND password = '" . $xpassword . "'");
               $IDCONTEO = $sentencia1->fetchObject();


               if (count($quser) > 0) {


                    $_SESSION["perfil"] = $IDCONTEO->perfil;   

                    $_SESSION["idU"] = $IDCONTEO->id;   

                    $_SESSION["username"] = $xusername;  

                    $_SESSION["whsInvs"] = $IDCONTEO->fk_ID_almacen_invs;  
                    
                    $_SESSION["whsTurem"] = $IDCONTEO->fk_ID_almacen_turemp; 
                    
                    $_SESSION["whsCica"] = $IDCONTEO->fk_ID_almacen_cierre; 

                    
                     
                     header("location:wllcm.php");  
                }

               else  
               {  
                    $message = '<label>Credenciales Incorrectas</label>';  
               }  
               
           }  
      }  
 }  
 catch(PDOException $error)  
 {  
      $message = $error->getMessage();  
 }  
 ?>  
 
 <!DOCTYPE html>  
 <html lang="en">
      <head>  
           <title>Acceso Inventarios</title>  
           <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
     <!--===============================================================================================-->	
          <link rel="icon" type="image/png" href="images/favicon.ico"/>
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
     <!--===============================================================================================-->	
          <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
     <!--===============================================================================================-->	
          <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
     <!--===============================================================================================-->
          <link rel="stylesheet" type="text/css" href="css/util.css">
          <link rel="stylesheet" type="text/css" href="css/main.css">
     <!--===============================================================================================-->

               
      </head>  
      <body>  
    
          <div class="limiter">
		<div class="container-login100">
		<div class="wrap-login100">
               
               
               <span class="login100-form-title p-b-26">
                    <h2 class="fw-bold mb-2 text-uppercase">Autenticación</h2>
                    <p class="text-white-50 mb-5">STORE CONTROL</p>
			</span>
 
                <form method="post" class="login100-form validate-form">  

               <div class="wrap-input100 validate-input"  data-validate="Enter username">      
                         <input type="text" name="iusername" id="iusername" 
                              onkeyup=" var start = this.selectionStart;
                              var end = this.selectionEnd;
                              this.value = this.value.toUpperCase();
                              this.setSelectionRange(start, end);"
                         class="input100" />  
                         <span class="focus-input100" data-placeholder="Usuario"></span>
               </div>
                    
                      
               <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <span class="btn-show-pass">
                         <i class="zmdi zmdi-eye"></i>
                    </span>
                    <input type="password" name="ipassword" class="input100"/>  
                    <span class="focus-input100" data-placeholder="Contraseña"></span>
               </div>               
                     
                     


                                    <?php  
                if(isset($message))  
                {  
                     echo '<label class="text-danger">'.$message.'</label>';  
                }  
                ?> 
                     <div class="container-login100-form-btn">
                         <div class="wrap-login100-form-btn">
                              <div class="login100-form-bgbtn"></div>
                              <button class="login100-form-btn" type="submit" name="login">
                                   Ingresar
                              </button>
                         </div>
                    </div>
                </form>  
 

                <div class="text-center p-t-115">
                <span class="txt1">
                         Es tu primer ingreso? Usa tu usuario como contraseña.
                    </span>
               </br>
                <span class="txt1">
                         Problemas con el inicio?
                    </span>

                    <a class="txt2" href="mailto:sistemas@sunsetcorpholding.com">
                         Informanos
                    </a>
               </div>

           </div>
		</div>
	     </div>
          <div id="dropDownSelect1"></div>
	
     <!--===============================================================================================-->
          <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
     <!--===============================================================================================-->
          <script src="vendor/animsition/js/animsition.min.js"></script>
     <!--===============================================================================================-->
          <script src="vendor/bootstrap/js/popper.js"></script>
          <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
     <!--===============================================================================================-->
          <script src="vendor/select2/select2.min.js"></script>
     <!--===============================================================================================-->
          <script src="vendor/daterangepicker/moment.min.js"></script>
          <script src="vendor/daterangepicker/daterangepicker.js"></script>
     <!--===============================================================================================-->
          <script src="vendor/countdowntime/countdowntime.js"></script>
     <!--===============================================================================================-->
          <script src="js/main.js"></script>
      </body>  
 </html>  
 