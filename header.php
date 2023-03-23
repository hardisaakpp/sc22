<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>StoreControl22</title>
    <link rel="icon" type="image/png" href="images/favicon.png"/>
    <meta name="description" content="StoreControl22">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">-->
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/lib/datatable/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
   
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->

</head>
<body>

<?php
    // Validating Session
    session_start();
    if(strlen($_SESSION['username'])==0)
    {
        header('location:index.php');
    }
    
    include_once "php/bd_StoreControl.php";
    date_default_timezone_set('America/Bogota');

    $userName = $_SESSION['username'];
    $userId = $_SESSION['idU'];
    $userAdmin = $_SESSION["perfil"];
    $whsInvs = $_SESSION["whsInvs"]


?>

<style>
 .loader-page {
    position: fixed;
    z-index: 25000;
    background: rgb(255, 255, 255);
    left: 0px;
    top: 0px;
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition:all .3s ease;
  }
  .loader-page::before {
    content: "";
    position: absolute;
    border: 2px solid rgb(50, 150, 176);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-sizing: border-box;
    border-left: 2px solid rgba(50, 150, 176,0);
    border-top: 2px solid rgba(50, 150, 176,0);
    animation: rotarload 1s linear infinite;
    transform: rotate(0deg);
  }
  @keyframes rotarload {
      0%   {transform: rotate(0deg)}
      100% {transform: rotate(360deg)}
  }
  .loader-page::after {
    content: "";
    position: absolute;
    border: 2px solid rgba(50, 150, 176,.5);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-sizing: border-box;
    border-left: 2px solid rgba(50, 150, 176, 0);
    border-top: 2px solid rgba(50, 150, 176, 0);
    animation: rotarload 1s ease-out infinite;
    transform: rotate(0deg);
  }
</style>
    <!-- Left Panel -->




    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                      
            <?php 
            if ($userAdmin==1) {   
            ?>
                      <!--//Dashboard-->
                        <li>
                            <a href="index.html"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>
                        </li>
                        <!--//Configuración-->

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Configuración</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="fa fa-users"></i><a href="userL.php">Usuarios</a></li>
                                        <li><i class="fa fa-home"></i><a href="whsL.php">Almacenes</a></li>
                            


                            </ul>
                        </li>
            <?php 
             }  
            ?>
            <?php 
            if ($userAdmin==1 || $userAdmin==3) {   
            ?>

                        <!--//Reportes-->

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Bodegas</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="ti ti-package"></i><a href="stTrMT.php">Stock Transitorias (MT)</a></li>
                                        <li><i class="ti ti-package"></i><a href="stTrCE.php">Stock Transitorias (CE)</a></li>
                            


                            </ul>
                        </li>

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti ti-check-box"></i>Toma Fisica Total</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="fa fa-file-o"></i><a href="loadTT.php">Generar</a></li>
                                        <li><i class="fa fa-barcode"></i><a href="TTList.php">Scanear</a></li>
                                        <li><i class="ti ti-reload"></i><a href="TTrefresh.php">Actualizar Stock</a></li>
                                        <li><i class="fa fa-hospital-o"></i><a href="TTListR.php">Reportes</a></li>


                            </ul>
                        </li>
            <?php 
             }  
            
 if ($userAdmin==4) {   
            ?>

                        <!--//Reportes-->

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Bodegas</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="ti ti-package"></i><a href="stTrMT.php">Stock Transitorias (MT)</a></li>
                                        <li><i class="ti ti-package"></i><a href="stTrCE.php">Stock Transitorias (CE)</a></li>
                            


                            </ul>
                        </li>

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti ti-check-box"></i>Toma Fisica Total</a>
                            <ul class="sub-menu children dropdown-menu">     
                                 
                                        <li><i class="fa fa-barcode"></i><a href="TTList.php">Scanear</a></li>



                            </ul>
                        </li>
            <?php 
             }  
            ?>

                        <!--//INVENTARIOS-->
                        <li class="menu-item-has-children dropdown">
                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square-o"></i>Inventarios</a>
                            <ul class="sub-menu children dropdown-menu">
                                <?php 
                                    if ($userAdmin==1) { // ADMIN   
                                        echo '
                                            <li><i class="fa fa-spinner"></i><a href="loadTF.php">Cargar tomas aleatorias</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="tfaL.php">Revisar tomas tiendas</a></li>
                                        ';
                                    } else if ($userAdmin==2){ // TIENDA
                                        echo '
                                            <li><i class="fa fa-pencil-square-o"></i><a href="tfaD.php">Toma fisica aleatoria</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="tfaHu.php">Historial Tomas fisicas</a></li>
                                        ';
                                    } else if ($userAdmin==3){ // INVENTARIO
                                        echo '
                                            
                                        ';
                                    }else if ($userAdmin==4){ // cuentaInventarios
                                        # code...
                                    }else if ($userAdmin==5){ // bodega

                                    }  
                                ?>

                                
                            </ul>
                        </li>
                        

                 
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- TOP Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">
            <div class="top-left">
                <div class="navbar-header">
                    <a class="navbar-brand" href="wllcm.php">StoreControl|22</a>
                    <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
                    <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
                </div>
            </div>
            <div class="top-right">
                <div class="header-menu">
                    <div class="header-left" style="margin: auto;">
                    Usuario: 
            <?php
                echo $userName . " [ G" . $userAdmin."-ID". $userId .  "]"; 
            ?>  
                    </div>

                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="#"><i class="fa fa-user"></i>My Profile</a>

                         <!--   <a class="nav-link" href="#"><i class="fa fa-bell-o"></i>Notifications <span class="count">13</span></a> -->

                            <a class="nav-link" href="psswrd.php"><i class="fa fa-key"></i>Cambiar clave</a>

                            <a class="nav-link" href="php/logout.php"><i class="fa fa-power-off"></i>Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </header><!-- /header -->
        <!-- Header-->

 
<!-- .content -->   

<div class="loader-page"></div>
      