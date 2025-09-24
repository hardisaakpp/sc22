<!doctype html>
 <html class="no-js" lang=""> 

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>StoreControl|22</title>
    <link rel="icon" type="image/png" href="images/favicon.png"/>
    <meta name="description" content="StoreControl22">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/lib/datatable/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mobile-responsive.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body>

<?php
    session_start();
    if(strlen($_SESSION['username'])==0)
    {
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
    $whsCica= $_SESSION["whsCica"];  
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

/* Mejoras de responsividad móvil */
@media (max-width: 768px) {
    /* Navegación lateral mejorada para móviles */
    .left-panel {
        width: 280px !important;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .left-panel.show {
        transform: translateX(0);
    }
    
    .right-panel {
        margin-left: 0 !important;
        width: 100% !important;
    }
    
    /* Header móvil optimizado */
    .header {
        padding: 10px 15px;
    }
    
    .navbar-brand {
        font-size: 18px !important;
        padding: 8px 0;
    }
    
    .header-left {
        font-size: 12px !important;
        margin: auto 5px !important;
    }
    
    .user-avatar {
        width: 35px !important;
        height: 35px !important;
    }
    
    /* Menú toggle más grande para touch */
    #menuToggle {
        padding: 12px;
        font-size: 18px;
        background: none;
        border: none;
        color: #333;
    }
    
    /* Elementos del menú más grandes para touch */
    .main-menu .nav li a {
        padding: 15px 20px !important;
        font-size: 16px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .sub-menu li a {
        padding: 12px 25px !important;
        font-size: 14px;
    }
    
    /* Dropdown mejorado */
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
    }
    
    /* Content padding para móviles */
    .content {
        padding: 15px 10px;
        margin-top: 10px;
    }
}

@media (max-width: 480px) {
    /* Ajustes para pantallas muy pequeñas */
    .navbar-brand {
        font-size: 16px !important;
    }
    
    .header-left {
        display: none; /* Ocultar info de usuario en pantallas muy pequeñas */
    }
    
    .content {
        padding: 10px 5px;
    }
    
    /* Botones más grandes para touch */
    .btn {
        min-height: 44px;
        font-size: 16px;
        padding: 12px 16px;
    }
    
    .btn-sm {
        min-height: 38px;
        font-size: 14px;
        padding: 10px 14px;
    }
}

/* Mejoras para tablets */
@media (min-width: 769px) and (max-width: 1024px) {
    .left-panel {
        width: 240px;
    }
    
    .right-panel {
        margin-left: 240px;
    }
    
    .content {
        padding: 20px 15px;
    }
}

/* Overlay para cerrar menú en móvil */
@media (max-width: 768px) {
    .mobile-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    
    .mobile-overlay.show {
        display: block;
    }
}
</style>
 
    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
               
                 <!--//Home-->
                    <li>
                        <a href="wllcm.php"><i class="menu-icon fa fa-home"></i>Inicio </a>
                    </li>

                
                    <?php 


    //CONFIGURACION SOLO SOPORTETI-->
        if ($userAdmin==1  && $userName=='SOPORTETI') {   
            echo '
            <li class="menu-item-has-children dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Configuración</a>
            <ul class="sub-menu children dropdown-menu">     
                        <li><i class="fa fa-users"></i><a href="userL.php">Usuarios</a></li>

            </ul>


            </li>';

        }  else  {     

//recepcion transferencias tiendas
                    if ($userAdmin==2 || $userName == 'RL-PSC' || $userName == 'OUT-LLG') {   
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Recepción</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><i class="ti ti-menu-alt"></i><a href="recL.php">Recepción Mercadería</a></li>

                                    <li><i class="ti ti-package"></i><a href="recPL.php">Picking</a></li>
                                   

                                    <li><i class="ti ti-layout-grid2"></i><a href="recH.php">Historial</a></li>
                                
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
                    if ($userAdmin==1) {   
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

                        if ($userAdmin==1 || $userAdmin==3 || $userAdmin==5) {   
                        
                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                                    <i class="menu-icon fa fa-list-alt"></i>Reportes
                                </a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><i class="ti ti-layout-grid2-alt"></i><a href="stTrMT.php">Transitorias (MT)</a></li>
                                    <li><i class="ti ti-layout-grid2-alt"></i><a href="stTrCE.php">Transitorias (CE)</a></li>
                                    <li><i class="ti ti-view-grid"></i><a href="stransitoriaItmAll.php">Transfers. Pendientes</a></li>
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
                                    <li><i class="ti ti-menu-alt"></i><a href="cediSot.php">Crea Lista D.</a></li>

                                    <li><i class="ti ti-package"></i><a href="cediGrpD.php">Eliminar Lista</a></li>
                                   

                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLdis.php">Distribución</a></li>
                                
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
                                    <li><i class="ti ti-menu-alt"></i><a href="cediSot.php">Crea Lista D.</a></li>

                                    <li><i class="ti ti-package"></i><a href="cediGrpD.php">Eliminar Lista</a></li>
                                    <li><i class="ti-dropbox"></i><a href="cediGrpL.php">Recolección</a></li>

                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLdis.php">Distribución</a></li>
                                    
                                </ul>
                            </li>
                        ';
                        }*/
            //CEDI                
                        if ($userAdmin==1 || $userAdmin==3 || $userAdmin==5) {   
                        
                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>CEDI</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><i class="ti ti-menu-alt"></i><a href="cediSot.php">Crea Lista D.</a></li>

                                    <li><i class="ti ti-package"></i><a href="cediGrpD.php">Eliminar Lista</a></li>
                                    <li><i class="ti-dropbox"></i><a href="cediGrpL.php">Recolección</a></li>

                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLdis.php">Distribución</a></li>
                                    <li><i class="ti ti-search"></i><a href="findTransfer.php">Buscar #Transferencia</a></li>
                                    <li><i class="ti ti-dropbox"></i><a href="soltr.php">Etiquetas</a></li>
                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLhis.php">Historial</a></li>
                                    <li><i class="ti ti-layout-grid2"></i><a href="odc.php">ODC</a></li>
                                    <li><i class="ti ti-layout-grid2"></i><a href="cediKxSc.php">Log Solicitudes</a></li>
                                </ul>
                            </li>
                        ';

                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>CEDI CE</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><i class="ti ti-menu-alt"></i><a href="cediSotCE.php">Crea Lista D.</a></li>

                                    <li><i class="ti ti-package"></i><a href="cediGrpDCE.php">Eliminar Lista</a></li>
                                    <li><i class="ti-dropbox"></i><a href="cediGrpLCE.php">Recolección</a></li>

                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLdisCE.php">Distribución</a></li>
                                    <li><i class="ti ti-search"></i><a href="findTransferCE.php">Buscar #Transferencia</a></li>
                                  
                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLhisCE.php">Historial</a></li>
                                     <li><i class="ti ti-layout-grid2"></i><a href="odcCE.php">ODC</a></li>
                                     <li><i class="ti ti-layout-grid2"></i><a href="cediKxScCE.php">Log Solicitudes</a></li>
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
                                    <li><i class="ti ti-menu-alt"></i><a href="cediSot.php">Crea Lista D.</a></li>

                                    <li><i class="ti ti-package"></i><a href="cediGrpD.php">Eliminar Lista</a></li>
                                    <li><i class="ti-dropbox"></i><a href="cediGrpL.php">Recolección</a></li>

                                    <li><i class="ti ti-layout-grid2"></i><a href="cediGrpLdis.php">Distribución</a></li>
                                    <li><i class="ti ti-control-shuffle"></i><a href="cediMv.php">Reubicación</a></li>
                                </ul>
                            </li>
                        ';
                        }
                */
        //Toma Fisica Total
            if ($userAdmin==1 || $userAdmin==3) { 
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
            }else if ($userAdmin==4) {   
                echo '
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
                    echo $userName . " [ G" . $userAdmin."-ID". $userId .  "]"; 
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

<script>
// Funcionalidad mejorada para el menú móvil
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const leftPanel = document.getElementById('left-panel');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const rightPanel = document.getElementById('right-panel');

    // Toggle del menú móvil
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleMobileMenu();
        });
    }

    // Cerrar menú al hacer click en el overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            closeMobileMenu();
        });
    }

    // Cerrar menú al cambiar el tamaño de ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });

    // Mejorar el comportamiento de los dropdowns en móvil
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                if (dropdown) {
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                }
            }
        });
    });
});

function toggleMobileMenu() {
    const leftPanel = document.getElementById('left-panel');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    if (leftPanel && mobileOverlay) {
        leftPanel.classList.toggle('show');
        mobileOverlay.classList.toggle('show');
        
        // Prevenir scroll del body cuando el menú está abierto
        if (leftPanel.classList.contains('show')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

function closeMobileMenu() {
    const leftPanel = document.getElementById('left-panel');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    if (leftPanel && mobileOverlay) {
        leftPanel.classList.remove('show');
        mobileOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Mejorar la experiencia de touch en botones
document.addEventListener('touchstart', function() {}, {passive: true});
</script>
      