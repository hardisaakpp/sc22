<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>StoreControl|22</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <meta name="description" content="StoreControl22">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</head>

<body>

    <?php
    session_start();
    if (strlen($_SESSION['username']) == 0) {
        header('location:index.php');
    }

    include_once "php/bd_StoreControl.php";
    include_once "php/bd_Biometricos.php";

    date_default_timezone_set('America/Bogota');

    $userName = $_SESSION['username'];
    $userId = $_SESSION['idU'];
    $userAdmin = $_SESSION["perfil"];
    $whsInvs = $_SESSION["whsInvs"];
    //$whsTurem = $_SESSION["whsTurem"];
    $whsBodega = $_SESSION["whsBodeg"];
    $whsCica = $_SESSION["whsCica"];
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
            transition: all .3s ease;
        }

        .loader-page::before {
            content: "";
            position: absolute;
            border: 2px solid rgb(50, 150, 176);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-sizing: border-box;
            border-left: 2px solid rgba(50, 150, 176, 0);
            border-top: 2px solid rgba(50, 150, 176, 0);
            animation: rotarload 1s linear infinite;
            transform: rotate(0deg);
        }

        @keyframes rotarload {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        .loader-page::after {
            content: "";
            position: absolute;
            border: 2px solid rgba(50, 150, 176, .5);
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

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default w-100">

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">

                    <!--//Home-->
                    <li>
                        <a href="wllcm.php"><i class="menu-icon fa fa-home"></i>Inicio </a>
                    </li>


                    <?php


                    //CONFIGURACION SOLO SOPORTETI-->
                    if ($userAdmin == 1  && $userName == 'SOPORTETI') {
                        echo '
            <li class="menu-item-has-children dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Configuración</a>
            <ul class="sub-menu children dropdown-menu">     
                        <li><i class="fa fa-users"></i><a href="userL.php">Usuarios</a></li>

            </ul>


            </li>';
                    } else {

                        //recepcion transferencias tiendas
                        if ($userAdmin == 2) {
                            echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Recepción</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="recL.php"><i class="ti ti-menu-alt"></i>Recepción Mercadería</a></li>

                                    <li><a href="recPL.php"><i class="ti ti-package"></i>Picking</a></li>
                                   

                                    <li><a href="recH.php"><i class="ti ti-layout-grid2"></i>Historial</a></li>
                                
                                </ul>
                            </li>
                        ';
                        }

                        //HORARIOS-->
                        /*  if ($userAdmin==1) { // ADMIN   
                            echo '
                            <li class="menu-item-has-children dropdown">
                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-th"></i>Horarios</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-spinner"></i><a href="turempY2.php">Reporte Turnos/mes</a></li>
                                <li><i class="fa fa-spinner"></i><a href="turempY3.php">Reporte Turnos/dia</a></li>
                                <li><i class="fa fa-eye"></i><a href="turempL.php">Maestro Horarios</a></li>
                            </ul>
                            </li>
                            ';
                        } else if ($userAdmin==2){ // TIENDA
                            echo '
                            <li class="menu-item-has-children dropdown">
                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-th"></i>Horarios</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-calendar"></i><a href="turempY.php">Turnos</a></li>
                                <li><i class="fa fa-upload"></i><a href="turempimp.php">Importar Turnos</a></li>
                                <li><i class="fa fa-eye"></i><a href="turempL.php">Maestro Horarios</a></li>
                            </ul>
                            </li>
                            ';
                        } */

                        //CONFIGURACION-->
                        if ($userAdmin == 1) {
                            echo '
                            <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Configuración</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="fa fa-users"></i><a href="userL.php">Usuarios</a></li>
                                        <li><i class="fa fa-home"></i><a href="whsL.php">Almacenes</a></li>
                                        <li><i class="fa fa-home"></i><a href="depbankL.php">Cuenta Financiera MT</a></li>
                                        <li><i class="fa fa-home"></i><a href="depbankLce.php">Cuenta Financiera CE</a></li>
                                        <li><i class="fa fa-refresh"></i><a href="confMerg.php">Actualiza SAP->SC </a></li>
                            </ul>
                            </li>';
                        }

                        //Reportes

                        if ($userAdmin == 1 || $userAdmin == 3 || $userAdmin == 5) {


                            echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                    <i class="menu-icon fa fa-list-alt"></i>Reportes
                                </a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="stTrMT.php"><i class="ti ti-layout-grid2-alt"></i>Transitorias (MT)</a></li>
                                    <li><a href="stTrCE.php"><i class="ti ti-layout-grid2-alt"></i>Transitorias (CE)</a></li>
                                    <li><a href="stransitoriaItmAll.php"><i class="ti ti-view-grid"></i>Transfers. Pendientes</a></li>
                                </ul>
                            </li>
                        ';
                        }

                        //Bodegas 2.0                
                        /*    if ($userAdmin==1 ) {   
                        
                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Bodega Demo</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="cediSot.php"><i class="ti ti-menu-alt"></i>Crea Lista D.</a></li>

                                    <li><a href="cediGrpD.php"><i class="ti ti-package"></i>Eliminar Lista</a></li>
                                   

                                    <li><a href="cediGrpLdis.php"><i class="ti ti-layout-grid2"></i>Distribución</a></li>
                                
                                </ul>
                            </li>
                        ';
                        }*/

                        //MIGRACION       
                        /*    if ($userAdmin==1 ) {   
                        
                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>MIGRACION</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="cediSot.php"><i class="ti ti-menu-alt"></i>Crea Lista D.</a></li>

                                    <li><a href="cediGrpD.php"><i class="ti ti-package"></i>Eliminar Lista</a></li>
                                    <li><a href="cediGrpL.php"><i class="ti ti-dropbox"></i>Recolección</a></li>

                                    <li><a href="cediGrpLdis.php"><i class="ti ti-layout-grid2"></i>Distribución</a></li>
                                    
                                </ul>
                            </li>
                        ';
                        }*/
                        //CEDI                
                        if ($userAdmin == 1 || $userAdmin == 3 || $userAdmin == 5) {


                            echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>CEDI</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="cediSot.php"><i class="ti ti-menu-alt"></i>Crea Lista D.</a></li>

                                    <li><a href="cediGrpD.php"><i class="ti ti-package"></i>Eliminar Lista</a></li>
                                    <li><a href="cediGrpL.php"><i class="ti ti-dropbox"></i>Recolección</a></li>

                                    <li><a href="cediGrpLdis.php"><i class="ti ti-layout-grid2"></i>Distribución</a></li>
                                    <li><a href="findTransfer.php"><i class="ti ti-search"></i>Buscar #Transferencia</a></li>
                                    <li><a href="soltr.php"><i class="ti ti-dropbox"></i>Etiquetas</a></li>
                                    <li><a href="cediGrpLhis.php"><i class="ti ti-layout-grid2"></i>Historial</a></li>
                                    <li><a href="odc.php"><i class="ti ti-layout-grid2"></i>ODC</a></li>
                                    <li><a href="cediKxSc.php"><i class="ti ti-layout-grid2"></i>Log Solicitudes</a></li>
                                </ul>
                            </li>
                        ';


                            echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>CEDI CE</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="cediSotCE.php"><i class="ti ti-menu-alt"></i>Crea Lista D.</a></li>

                                    <li><a href="cediGrpDCE.php"><i class="ti ti-package"></i>Eliminar Lista</a></li>
                                    <li><a href="cediGrpLCE.php"><i class="ti ti-dropbox"></i>Recolección</a></li>

                                    <li><a href="cediGrpLdisCE.php"><i class="ti ti-layout-grid2"></i>Distribución</a></li>
                                    <li><a href="findTransferCE.php"><i class="ti ti-search"></i>Buscar #Transferencia</a></li>
                                  
                                    <li><a href="cediGrpLhisCE.php"><i class="ti ti-layout-grid2"></i>Historial</a></li>
                                     <li><a href="odcCE.php"><i class="ti ti-layout-grid2"></i>ODC</a></li>
                                     <li><a href="cediKxScCE.php"><i class="ti ti-layout-grid2"></i>Log Solicitudes</a></li>
                                </ul>
                            </li>
                        ';
                        }
                        //CEDI 2.0                
                        /*          if ($userAdmin==1 ) {   
                        
                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>CEDI 2</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><a href="cediSot.php"><i class="ti ti-menu-alt"></i>Crea Lista D.</a></li>

                                    <li><a href="cediGrpD.php"><i class="ti ti-package"></i>Eliminar Lista</a></li>
                                    <li><a href="cediGrpL.php"><i class="ti ti-dropbox"></i>Recolección</a></li>

                                    <li><a href="cediGrpLdis.php"><i class="ti ti-layout-grid2"></i>Distribución</a></li>
                                    <li><a href="cediMv.php"><i class="ti ti-control-shuffle"></i>Reubicación</a></li>
                                </ul>
                            </li>
                        ';
                        }
                */
                        //Toma Fisica Total
                        if ($userAdmin == 1 || $userAdmin == 3) {
                            echo '
                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti ti-check-box"></i>Toma Fisica Total</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="fa fa-file-o"></i><a href="loadTT.php">Generar</a></li>
                                        <li><i class="fa fa-barcode"></i><a href="TTList.php">Scanear</a></li>
                                        <li><i class="fa fa-hospital-o"></i><a href="TTListR.php">Reportes</a></li>


                            </ul>
                        </li>
                    ';
                        } else if ($userAdmin == 4) {
                            echo '
                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Bodegas</a>
                            <ul class="sub-menu children dropdown-menu">     
                                        <li><a href="stTrMT.php"><i class="ti ti-package"></i>Stock Transitorias (MT)</a></li>
                                        <li><a href="stTrCE.php"><i class="ti ti-package"></i>Stock Transitorias (CE)</a></li>
                                        <li><a href="stransitoriaItmAll.php"><i class="ti ti-view-grid"></i>Transfers. Pendientes</a></li>
                            </ul>
                        </li>

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti ti-check-box"></i>Toma Fisica Total</a>
                            <ul class="sub-menu children dropdown-menu">     
                                 
                                        <li><i class="fa fa-barcode"></i><a href="TTList.php">Scanear</a></li>



                            </ul>
                        </li>
                    ';
                        }
                    }
                    ?>


                </ul>
            </div>
        </nav>

    </aside>
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
                        echo $userName . " [ G" . $userAdmin . "-ID" . $userId .  "]";
                        ?>
                    </div>

                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="perfil.php"><i class="fa fa-user"></i>Mi perfil</a>
                            <a class="nav-link" href="psswrd.php"><i class="fa fa-key"></i>Cambiar clave</a>
                            <a class="nav-link" href="php/logout.php"><i class="fa fa-power-off"></i>Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- .content -->
        <div class="loader-page"></div>
        <div class="mobile-overlay" onclick="closeMobileMenu()"></div>