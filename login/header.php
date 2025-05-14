<?php session_start(); if(!isset($_SESSION['username']) || !isset($_SESSION['perfil'])){ session_unset(); session_destroy(); header('location:index.php'); exit(); } ?>
<!doctype html>
<html class="no-js" lang="es" data-bs-theme="auto">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>StoreControl22</title>
        <link rel="icon" type="image/png" href="images/favicon.png"/>
        <meta name="description" content="StoreControl22">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
        <script src="../assets/dist/js/color-modes.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="icon" href="assets/img/store_control/favicon.ico" type="image/x-icon">
        <link href="../assets/dist/css/checkout.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
        <link href="https://fonts.googleapis.com/css2?family=Mont&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/dist/css/cs-skin-elastic.css">
        <link rel="stylesheet" href="assets/lib/datatable/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="assets/dist/css/style.css">    
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
        <script>$(document).ready(function(){ $('#employee_data').DataTable(); });</script> 
        <style>
            body {
            background-color: var(--bs-body-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            transition: background-color 0.3s ease;
            font-family: 'Mont', sans-serif;
            animation: fadeIn 1s ease-out;
            }

            .card.login-card {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            border: none;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 2rem 2rem;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            animation: slideFade 0.8s ease-out;
            }

            .card.login-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
            }

            h3 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            }

            .form-control {
            border-radius: 8px;
            padding: 0.65rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }

            .form-control:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.2);
            }

            .login100-form-btn {
            background-color: #6f42c1;
            border: none;
            border-radius: 8px;
            padding: 0.6rem;
            font-weight: 500;
            transition: background-color 0.2s ease, transform 0.2s ease;
            }

            .login100-form-btn:hover {
            background-color: #59359c;
            transform: translateY(-1px);
            }

            .alert {
            border-radius: 6px;
            animation: fadeIn 0.5s ease;
            font-size: 0.95rem;
            }

            small {
            color: var(--bs-secondary-color);
            }

            @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
            }

            @keyframes slideFade {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
            }          
        </style>
        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }

            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                font-size: 3.5rem;
                }
            }

            .b-example-divider {
                width: 100%;
                height: 2rem;
                background-color: rgba(0, 0, 0, .1);
                border: solid rgba(0, 0, 0, .15);
                border-width: 1px 0;
                box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
            }

            .b-example-vr {
                flex-shrink: 0;
                width: 1.5rem;
                height: 100vh;
            }

            .bi {
                vertical-align: -.125em;
                fill: currentColor;
            }

            .nav-scroller {
                position: relative;
                z-index: 2;
                height: 2.75rem;
                overflow-y: hidden;
            }

            .nav-scroller .nav {
                display: flex;
                flex-wrap: nowrap;
                padding-bottom: 1rem;
                margin-top: -1px;
                overflow-x: auto;
                text-align: center;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }

            .btn-bd-primary {
                --bd-violet-bg: #712cf9;
                --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

                --bs-btn-font-weight: 600;
                --bs-btn-color: var(--bs-white);
                --bs-btn-bg: var(--bd-violet-bg);
                --bs-btn-border-color: var(--bd-violet-bg);
                --bs-btn-hover-color: var(--bs-white);
                --bs-btn-hover-bg: #6528e0;
                --bs-btn-hover-border-color: #6528e0;
                --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
                --bs-btn-active-color: var(--bs-btn-hover-color);
                --bs-btn-active-bg: #5a23c8;
                --bs-btn-active-border-color: #5a23c8;
            }

            .bd-mode-toggle {
                z-index: 1500;
            }

            .bd-mode-toggle .dropdown-menu .active .bi {
                display: block !important;
            }
        </style>
    </head>

    <body data-bs-theme="auto">
        <?php
            if(strlen($_SESSION['username'])==0){ session_unset(); session_destroy(); header('location:index.php'); }
            include_once "../cx/bd_scs.php";
            include_once "php/bd_StoreControl.php";
            include_once "php/bd_Biometricos.php";            
            date_default_timezone_set('America/Bogota');
            $userName = $_SESSION['username'];
            $userId = $_SESSION['idU'];
            $userAdmin = $_SESSION["perfil"];
            $whsInvs = $_SESSION["whsInvs"];
            $whsTurem = $_SESSION["whsTurem"];
            $whsCica= $_SESSION["whsCica"];  //tambien para preven
        ?>

        <svg xmlns="#" class="d-none">
            <symbol id="check2" viewBox="0 0 16 16">
                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
            </symbol>
            <symbol id="circle-half" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
            </symbol>
            <symbol id="moon-stars-fill" viewBox="0 0 16 16">
                <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
                <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
            </symbol>
            <symbol id="sun-fill" viewBox="0 0 16 16">
                <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
            </symbol>
        </svg>

        <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
            <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
                <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
                <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
                <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                    <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#sun-fill"></use></svg>
                    Claro
                    <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
                </button>
                </li>
                <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                    <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
                    Oscuro
                    <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
                </button>
                </li>
                <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
                    <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#circle-half"></use></svg>
                    Automático
                    <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
                </button>
                </li>
            </ul>
        </div>

        <aside id="left-panel" class="left-panel bg-body text-body">
            <nav class="navbar navbar-expand-sm bg-body">
                <div id="main-menu" class="main-menu collapse navbar-collapse">
                    <ul class="nav navbar-nav">             
                        <li><a href="wllcm.php"><i class="menu-icon fa fa-home"></i>Inicio </a></li>
                        <?php 
                            //CONFIGURACION SOLO SOPORTETI-->
                            if($userAdmin==1 && $userName=='SOPORTETI'){   
                                echo '
                                <li class="menu-item-has-children dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Configuración</a>
                                    <ul class="sub-menu children dropdown-menu">     
                                        <li><i class="fa fa-users"></i><a href="userL.php">Usuarios</a></li>

                                    </ul>
                                </li>';                
                            }else{
                                //INVENTARIOS TFA-->
                                if($userAdmin==1 && $userName<>'SISTEMAS'){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-spinner"></i><a href="loadTF.php">Cargar tomas aleatorias</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="tfaL.php">Revisar tomas tiendas</a></li>
                                        </ul>
                                    </li>
                                    ';
                                }elseif($userName==='SISTEMAS'){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                    <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                                    <ul class="sub-menu children dropdown-menu">
                                        <li><i class="fa fa-spinner"></i><a href="tfaRe.php">Log Tomas aleatorias</a></li>
                                        <li><i class="fa fa-spinner"></i><a href="loadTF.php">Cargar tomas aleatorias</a></li>
                                        <li><i class="fa fa-h-square"></i><a href="tfaL.php">Revisar tomas tiendas</a></li>
                                    </ul>
                                    </li>
                                    ';
                                }elseif($userAdmin==2){ // TIENDA
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-pencil-square-o"></i><a href="tfaD.php">Toma aleatoria</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="tfaHu.php">Historial</a></li>
                                        </ul>
                                    </li>
                                    ';
                                }elseif($userAdmin==3){ // INVENTARIO
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-spinner"></i><a href="loadTF.php">Cargar tomas aleatorias</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="tfaL.php">Revisar tomas tiendas</a></li>
                                        </ul>
                                    </li>                                        
                                    ';
                                }elseif($userAdmin==4){ // cuentaInventarios
                                    # code...
                                }elseif($userAdmin==5){ // bodega
                                }  

                                //PRESUPUESTOS TFA
                                $LP = "LP"; 
                                $SAL = "SAL";
                                $RL = "RL";
                                $OUT = "OUT";
                                $YHD = "YHD";
                                if($userAdmin==1){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-spinner"></i><a href="prevenImp.php">Cargar COSMEC</a></li>
                                            <li><i class="fa fa-spinner"></i><a href="prevenImpRL.php">Cargar ROLAND</a></li>
                                            <li><i class="fa fa-spinner"></i><a href="prevenImpLP.php">Cargar LILI PINK</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenL.php">Revisar COSMEC</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenLRL.php">Revisar ROLAND</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenLLP.php">Revisar LILI PINK</a></li>                                       
                                        </ul>
                                    </li>
                                    ';
                                }elseif($userAdmin==2){ // TIENDA
                                    if(substr($userName,0,strlen($RL))===$RL || substr($userName,0,strlen($OUT))===$OUT || substr($userName,0,strlen($YHD))===$YHD){
                                        echo '
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-spinner"></i><a href="prevenImpRLt.php">Cargar</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLt.php">Revisar Metas</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLmDAY.php">Ventas Día/Asesor</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLm.php">Cumplimiento</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenList.php">Maestro Vendedores</a></li>
                                            </ul>
                                        </li>';
                                    }elseif(substr($userName,0,strlen($LP))===$LP){
                                        echo '
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-spinner"></i><a href="prevenImpRLt.php">Cargar</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLt.php">Revisar Metas</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLmDAY.php">Ventas Día/Asesor</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLm.php">Cumplimiento</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenList.php">Maestro Vendedores</a></li>       
                                            </ul>
                                        </li>';
                                    }else{
                                        echo '
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-spinner"></i><a href="prevenImp.php">Cargar COSMEC</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenL.php">Revisar COSMEC</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLmDAY.php">Ventas Día/Asesor</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenLRLm.php">Cumplimiento</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="prevenListC.php">Maestro Vendedores COSMEC</a></li>
                                            </ul>
                                        </li>';                                            
                                    }  
                                }elseif($userId==274){ // TIENDA
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                        <ul class="sub-menu children dropdown-menu">                                        
                                            <li><i class="fa fa-spinner"></i><a href="prevenImpLP.php">Cargar LILI PINK</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenLLP.php">Revisar LILI PINK</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenListC.php">Maestro Vendedores</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userId==275){ // TIENDA
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                        <ul class="sub-menu children dropdown-menu">                                    
                                            <li><i class="fa fa-spinner"></i><a href="prevenImpRL.php">Cargar ROLAND</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenLRL.php">Revisar ROLAND</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenListC.php">Maestro Vendedores</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userId==276){ // TIENDA
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                        <ul class="sub-menu children dropdown-menu">                                    
                                            <li><i class="fa fa-spinner"></i><a href="prevenImp.php">Cargar COSMEC</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenL.php">Revisar COSMEC</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="prevenListC.php">Maestro Vendedores</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==2){ // TIENDA                               
                                }elseif($userAdmin==2){ // TIENDA
                                }elseif($userAdmin==3){ // INVENTARIO
                                }elseif($userAdmin==4){ // cuentaInventarios
                                }elseif($userAdmin==5){ // bodega
                                }  

                                //HORARIOS-->
                                if($userAdmin==1){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-th"></i>Horarios</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-spinner"></i><a href="turempY2.php">Reporte Turnos/mes</a></li>
                                            <li><i class="fa fa-spinner"></i><a href="turempY3.php">Reporte Turnos/dia</a></li>
                                            <li><i class="fa fa-eye"></i><a href="turempL.php">Maestro Horarios</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==2){ // TIENDA
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-th"></i>Horarios</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-calendar"></i><a href="turempY.php">Turnos</a></li>
                                            <li><i class="fa fa-upload"></i><a href="turempimp.php">Importar Turnos</a></li>
                                            <li><i class="fa fa-eye"></i><a href="turempL.php">Maestro Horarios</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==3){ // INVENTARIO
                                }elseif($userAdmin==4){ // cuentaInventarios
                                }elseif($userAdmin==5){ // bodega
                                }  
                                
                                //CIERRES DE CAJA-->                    
                                if(substr($userName,0,strlen($OUT))===$OUT || substr($userName,0,strlen($RL))===$RL){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                            <!--<li><i class="fa fa-pencil-square-o"></i><a href="cic.php?pFecha=2024-10-29&pIdAlmacen='.$whsCica.'">Cierre de Caja (AYER)</a></li> -->
                                            <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial</a></li>
                                        </ul>
                                    </li>';
                                }elseif(substr($userName,0,strlen($LP))===$LP){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                            <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==1){ // ADMIN   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicL.php">Cierres de caja (HITELL)</a></li>
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaL.php">Cierres de caja (MT)</a></li>
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaLlp.php">Cierres de caja (LP-HITELL)</a></li>
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaLce.php">Cierres de caja (CE)</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==2){ // TIENDA
                                    if(substr($userName,0,strlen($LP))===$LP){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">            
                                                <li><i class="fa fa-pencil-square-o"></i><a href="hcica.php">Cierre de Caja H</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="hcicaHu.php">Historial H</a></li>
                                            </ul>
                                        </li>';
                                    }elseif(substr($userName,0,strlen($SAL))===$SAL){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-MSO'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='CL-SMA'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='PSB-DOR'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';                                                                
                                    }elseif($userName=='PSB-MPA'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='PSB-PSM'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-DOR'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-SMA'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='PSB-MSO'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='PSB-SMA'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-ISL'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-SCA') {
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='PDP-QN'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-JAR'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='CL-QSN'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='PSB-QN'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }elseif($userName=='MC-QSN'){
                                        echo ' 
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cic.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicHu.php">Historial (H)</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';
                                    }else{
                                        echo '
                                        <li class="menu-item-has-children dropdown">
                                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                            <ul class="sub-menu children dropdown-menu">
                                                <li><i class="fa fa-pencil-square-o"></i><a href="cica.php">Cierre de Caja</a></li>
                                                <li><i class="fa fa-h-square"></i><a href="cicahu.php">Historial</a></li>
                                            </ul>
                                        </li>';                                            
                                    }
                                }elseif($userAdmin==6 && $userName=='CONTABILIDADMT'){ // INVENTARIO
                                    echo '
                                        <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                        <ul class="sub-menu children dropdown-menu">                                        
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicL.php">Cierres de caja (HITELL)</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==6 && $userName=='CONTABILIDADCE'){ // INVENTARIO
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaLce.php">Cierres de caja (CE)</a></li>
                                            <li><i class="fa fa-pencil-square-o"></i><a href="cicLce.php">Cierres de caja (CE-HITELL)</a></li>
                                        </ul>
                                    </li>';
                                }elseif($userAdmin==4){ // cuentaInventarios
                                }elseif($userAdmin==5){ // bodega
                                }  

                                //CONFIGURACION-->
                                if($userAdmin==1){   
                                    echo '
                                    <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Configuración</a>
                                        <ul class="sub-menu children dropdown-menu">     
                                            <li><i class="fa fa-users"></i><a href="userL.php">Usuarios</a></li>
                                            <li><i class="fa fa-home"></i><a href="whsL.php">Almacenes</a></li>
                                        </ul>
                                    </li>';                    
                                }
                                if($userAdmin==1 || $userAdmin==3 || $userAdmin==5){ ?>
                                    <!--//Reportes-->
                                    <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Bodegas</a>
                                        <ul class="sub-menu children dropdown-menu">     
                                            <li><i class="ti ti-dropbox"></i><a href="soltr.php">Solicitud Translado</a></li>
                                            <li><i class="fa fa-print"></i><a href="soltrL.php">Etiquetas</a></li>
                                            <li><i class="ti ti-package"></i><a href="stTrMT.php">Stock Transitorias (MT)</a></li>
                                            <li><i class="ti ti-package"></i><a href="stTrCE.php">Stock Transitorias (CE)</a></li>
                                            <li><i class="ti ti-package"></i><a href="stransitoriaItmAll.php">Transfers. Pendientes</a></li>
                                        </ul>
                                    </li>
                                <?php }
                                if($userAdmin==1 || $userAdmin==3){ ?>
                                    <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti ti-check-box"></i>Toma Fisica Total</a>
                                        <ul class="sub-menu children dropdown-menu">     
                                            <li><i class="fa fa-file-o"></i><a href="loadTT.php">Generar</a></li>
                                            <li><i class="fa fa-barcode"></i><a href="TTList.php">Scanear</a></li>
                                            <li><i class="ti ti-reload"></i><a href="TTrefresh.php">Actualizar Stock</a></li>
                                            <li><i class="fa fa-hospital-o"></i><a href="TTListR.php">Reportes</a></li>
                                        </ul>
                                    </li>
                                <?php }
                                if($userAdmin==4){ ?>
                                    <!--//Reportes-->
                                    <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Bodegas</a>
                                        <ul class="sub-menu children dropdown-menu">     
                                            <li><i class="ti ti-package"></i><a href="stTrMT.php">Stock Transitorias (MT)</a></li>
                                            <li><i class="ti ti-package"></i><a href="stTrCE.php">Stock Transitorias (CE)</a></li>
                                            <li><i class="ti ti-package"></i><a href="stransitoriaItmAll.php">Transfers. Pendientes</a></li>
                                        </ul>
                                    </li>
                                    <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti ti-check-box"></i>Toma Fisica Total</a>
                                        <ul class="sub-menu children dropdown-menu"> 
                                            <li><i class="fa fa-barcode"></i><a href="TTList.php">Scanear</a></li>
                                        </ul>
                                    </li>
                                <?php }  
                            } 
                        ?>
                    </ul>
                </div>           
            </nav>         
        </aside>    

        <div id="right-panel" class="right-panel">
            <header id="header" class="header bg-body text-body shadow-sm">
                <div class="top-left bg-body text-body">
                    <div class="navbar-header d-flex align-items-center justify-content-between px-1 py-1 bg-body text-body">
                        <a class="navbar-brand text-body" href="wllcm.php"><h6>StoreControl|25</h6></a>    
                        <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>    
                        <a id="menuToggle" class="menutoggle text-body"><i class="fa fa-bars"></i></a>
                    </div>
                </div>
                <div class="top-right bg-body text-body">
                    <div class="header-menu">
                        <div class="header-left" style="margin: auto;">
                            Usuario: <?php echo $userName . " [ G" . $userAdmin."-ID". $userId .  "]"; ?>  
                        </div>
                        <div class="user-area dropdown float-right">
                            <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                </svg>
                            </a>
                            <div class="user-menu dropdown-menu">
                                <a class="nav-link" href="psswrd.php"><i class="fa fa-key"></i>Cambiar clave</a>
                                <a class="nav-link" href="logout.php"><i class="fa fa-power-off"></i>Cerrar Sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        <div class="loader-page"></div>