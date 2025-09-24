<!doctype html>
 <html class="no-js" lang=""> 

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>StoreControl|22</title>
    <link rel="icon" type="image/png" href="images/favicon.png"/>
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

/* Navegación móvil mejorada con overlay */
@media (max-width: 768px) {
    /* Left panel como overlay fijo */
    .left-panel {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 280px !important;
        height: 100vh !important;
        z-index: 9999 !important;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        box-shadow: 2px 0 15px rgba(0,0,0,0.3);
        background: #fff !important;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .left-panel.show {
        transform: translateX(0);
    }
    
    /* Right panel ocupa todo el ancho */
    .right-panel {
        margin-left: 0 !important;
        width: 100% !important;
        position: relative;
        padding: 0 0;
    }
    
    /* Header mejorado para móviles - Todo alineado a la izquierda */
    .header {
        position: relative;
        z-index: 1000;
        padding: 8px 10px 8px 0;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: flex-start; /* Alinear todo a la izquierda */
    }
    
    /* Top-left expandido para ocupar más espacio */
    .top-left {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 6px; /* Reducir gap para acercar user-area al hamburguesa */
        justify-content: flex-start;
    }
    
    .navbar-header {
        display: flex;
        align-items: center;
        gap: 8px; /* Reducir gap para elementos más compactos */
        flex: 1;
    }
    
    /* Top-right alineado a la izquierda junto con el resto */
    .top-right {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        margin-left: 12px; /* Pequeño espacio desde el logo, no auto */
    }
    
    /* Menú toggle más prominente y táctil */
    #menuToggle {
        order: -1; /* Colocar el botón hamburguesa primero */
        padding: 10px 10px 10px 5px; /* Menos padding izquierdo */
        font-size: 22px;
        background: none;
        border: none;
        color: #333;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s ease;
        min-width: 44px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    #menuToggle:hover, #menuToggle:focus {
        background-color: rgba(0,123,255,0.1);
        color: #007bff;
        outline: none;
    }
    
    #menuToggle:active {
        transform: scale(0.95);
        background-color: rgba(0,123,255,0.2);
    }
    
    /* Logo/Brand responsivo */
    .navbar-brand {
        font-size: 18px !important;
        font-weight: 600;
        color: #333 !important;
        text-decoration: none !important;
        padding: 8px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px; /* Reducir para dar más espacio al header-left */
        order: 0; /* Mantener el logo en posición normal */
        flex-shrink: 1; /* Permitir que se comprima si es necesario */
    }
    
    .navbar-brand:hover {
        color: #007bff !important;
        text-decoration: none !important;
    }
    
    /* Top-right responsivo para móviles */
    .top-right {
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }
    
    .header-menu {
        display: flex;
        align-items: center;
        gap: 4px; /* Reducir gap para acercar elementos */
        width: auto; /* Cambiar de 100% a auto para no ocupar todo el ancho */
        flex-wrap: nowrap;
        justify-content: flex-start; /* Alinear contenido a la izquierda */
    }
    
    .header-left {
        font-size: 17px !important; /* Aumentado para mejor legibilidad en móviles */
        color: #666;
        margin: 0 !important;
        white-space: nowrap;
        overflow: visible; /* Permitir que se vea todo el contenido */
        text-overflow: initial; /* No truncar con ellipsis */
        max-width: none; /* Sin límite de ancho */
        display: block !important; /* Asegurar que se muestre en móviles */
        order: -1; /* Posicionar después del avatar pero antes del logo */
        flex-shrink: 0; /* No permitir que se comprima */
    }
    
    /* User area optimizado para móviles - Sin espacios a la izquierda */
    .user-area {
        position: relative;
        margin-left: 0; /* Eliminar margen izquierdo */
        order: -2; /* Mover avatar hacia la izquierda en móviles, después del hamburguesa */
    }
    
    .user-area .dropdown-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2px; /* Reducir padding para acercar más a la izquierda */
        border-radius: 50%;
        transition: all 0.2s ease;
        min-width: 44px;
        min-height: 44px;
        background: none;
        border: none;
        text-decoration: none !important;
    }
    
    .user-area .dropdown-toggle:hover,
    .user-area .dropdown-toggle:focus {
        background-color: rgba(0,123,255,0.1);
        transform: scale(1.05);
        outline: none;
    }
    
    .user-avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50%;
        border: 2px solid #e9ecef;
        transition: border-color 0.2s ease;
    }
    
    .user-area:hover .user-avatar {
        border-color: #007bff;
    }
    
    /* Dropdown del usuario mejorado */
    .user-menu {
        min-width: 180px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 8px 0;
        margin-top: 8px;
    }
    
    .user-menu .nav-link {
        padding: 10px 16px;
        font-size: 14px;
        color: #333;
        transition: all 0.2s ease;
        border-radius: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .user-menu .nav-link:hover {
        background-color: #f8f9fa;
        color: #007bff;
        text-decoration: none;
    }
    
    .user-menu .nav-link i {
        width: 16px;
        text-align: center;
    }
    
    /* Overlay para cerrar menú */
    .mobile-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .mobile-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    /* Elementos del menú optimizados para touch */
    .main-menu .nav li a {
        padding: 15px 20px !important;
        font-size: 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }
    
    .main-menu .nav li a:hover {
        background-color: #f8f9fa;
    }
    
    .sub-menu li a {
        padding: 12px 25px !important;
        font-size: 14px;
    }
    
    /* Prevenir scroll del body cuando el menú está abierto */
    body.menu-open {
        overflow: hidden;
    }
    
    /* Prevenir conflictos con Bootstrap navbar-collapse */
    @media (max-width: 768px) {
        .navbar-collapse {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            height: auto !important;
            overflow: visible !important;
        }
        
        .navbar-collapse.collapse {
            display: block !important;
        }
        
        .navbar-collapse.collapsing {
            display: block !important;
        }
        
        /* Asegurar que nuestro sistema tenga prioridad */
        .left-panel {
            display: block !important;
        }
        
        .left-panel:not(.show) {
            transform: translateX(-100%) !important;
        }
        
        .left-panel.show {
            transform: translateX(0) !important;
        }
    }
}

/* Estilos para tablets - Elementos más concentrados a la izquierda */
@media (min-width: 769px) and (max-width: 1024px) {
    .header {
        padding: 10px 15px 10px 0; /* Eliminar padding izquierdo en tablets */
        min-height: 65px;
        justify-content: flex-start;
    }
    
    .top-left {
        justify-content: flex-start;
    }
    
    .top-right {
        margin-left: 15px; /* Espacio desde el logo, no desde el borde derecho */
    }
    
    .navbar-brand {
        font-size: 20px !important;
    }
    
    .header-left {
        font-size: 14px; /* Aumentado para mejor legibilidad en tablets */
        max-width: none; /* Sin límite de ancho */
        display: block !important;
        overflow: visible;
        text-overflow: initial;
        flex-shrink: 0;
    }
    
    .header-menu {
        gap: 12px;
    }
    
    .user-area .dropdown-toggle {
        min-width: 46px;
        min-height: 46px;
    }
    
    .user-avatar {
        width: 38px !important;
        height: 38px !important;
    }
    
    .user-menu {
        min-width: 190px;
    }
    
    .user-menu .nav-link {
        padding: 12px 18px;
        font-size: 15px;
    }
    
    /* Restablecer orden normal en tablets */
    .user-area {
        order: 0; /* Volver a posición normal */
    }
    
    .header-left {
        order: 0; /* Volver a posición normal */
    }
}

@media (max-width: 480px) {
    .left-panel {
        width: 260px !important;
    }
    
    /* Header ultra-compacto para pantallas pequeñas - Todo a la izquierda */
    .header {
        padding: 5px 8px 5px 0; /* Eliminar padding izquierdo completamente */
        min-height: 55px;
        justify-content: flex-start;
    }
    
    .top-left {
        gap: 4px; /* Gap mínimo para pantallas pequeñas */
        flex: 1;
        justify-content: flex-start;
    }
    
    .navbar-header {
        gap: 4px; /* Gap mínimo para pantallas pequeñas */
        justify-content: flex-start;
    }
    
    /* Top-right muy compacto, pegado al contenido izquierdo */
    .top-right {
        margin-left: 8px; /* Muy poco espacio desde el logo */
        padding: 0 0;
    }
    
    .navbar-brand {
        font-size: 16px !important;
        max-width: 150px;
    }
    
    #menuToggle {
        min-width: 40px;
        min-height: 40px;
        font-size: 20px;
        padding: 8px 8px 8px 2px; /* Menos padding izquierdo en pantallas pequeñas */
    }
    
    /* Top-right ultra-compacto para pantallas pequeñas */
    .top-right {
        flex-shrink: 0;
    }
    
    .header-menu {
        gap: 2px; /* Gap mínimo para pantallas pequeñas */
    }
    
    .header-left {
        font-size: 12px !important; /* Aumentado para mejor legibilidad */
        max-width: 180px; /* Más ancho para mostrar más contenido */
        display: block !important;
        overflow: visible;
        text-overflow: initial;
        flex-shrink: 0;
    }
    
    .user-area {
        margin-left: 0; /* Eliminar margen para pantallas pequeñas */
    }
    
    .user-area .dropdown-toggle {
        min-width: 40px;
        min-height: 40px;
        padding: 2px;
    }
    
    .user-avatar {
        width: 32px !important;
        height: 32px !important;
        border-width: 1px;
    }
    
    /* Dropdown más compacto en pantallas pequeñas */
    .user-menu {
        min-width: 160px;
        margin-top: 5px;
    }
    
    .user-menu .nav-link {
        padding: 8px 12px;
        font-size: 13px;
        gap: 8px;
    }
}

/* Optimizaciones para landscape en móviles - Elementos a la izquierda */
@media (max-height: 500px) and (orientation: landscape) {
    .header {
        padding: 4px 8px 4px 0; /* Eliminar padding izquierdo en landscape */
        min-height: 50px;
        justify-content: flex-start;
    }
    
    .top-left {
        justify-content: flex-start;
        gap: 6px;
    }
    
    .navbar-header {
        gap: 6px;
    }
    
    .top-right {
        margin-left: 6px; /* Muy pegado en landscape */
    }
    
    .navbar-brand {
        font-size: 16px !important;
    }
    
    .header-left {
        font-size: 11px !important; /* Aumentado para mejor legibilidad en landscape */
        max-width: 150px; /* Más ancho para landscape */
        display: block !important;
        overflow: visible;
        text-overflow: initial;
        flex-shrink: 0;
    }
    
    #menuToggle {
        min-width: 38px;
        min-height: 38px;
        font-size: 18px;
        padding: 6px 6px 6px 2px; /* Menos padding izquierdo en landscape */
    }
    
    .user-area .dropdown-toggle {
        min-width: 38px;
        min-height: 38px;
    }
    
    .user-avatar {
        width: 30px !important;
        height: 30px !important;
    }
    
    /* Restablecer orden normal en landscape */
    .user-area {
        order: 0; /* Posición normal en landscape */
    }
    
    .header-left {
        order: 0; /* Posición normal en landscape */
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
// Navegación móvil mejorada con overlay - Versión estable
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const leftPanel = document.getElementById('left-panel');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    // Variable para prevenir conflictos
    let menuIsToggling = false;

    // Toggle del menú móvil con protección contra doble click
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (menuIsToggling) return;
            menuIsToggling = true;
            
            toggleMobileMenu();
            
            setTimeout(() => {
                menuIsToggling = false;
            }, 300);
        });
    }

    // Cerrar menú al hacer click en el overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function(e) {
            e.stopPropagation();
            closeMobileMenu();
        });
    }

    // Cerrar menú con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && leftPanel && leftPanel.classList.contains('show')) {
            closeMobileMenu();
        }
    });

    // Cerrar menú al cambiar el tamaño de ventana con debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        }, 150);
    });

    // Mejorar el comportamiento de los dropdowns en móvil con mejor control
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdown = this.nextElementSibling;
                if (dropdown && dropdown.classList.contains('sub-menu')) {
                    const isOpen = dropdown.style.display === 'block';
                    
                    // Cerrar todos los otros dropdowns
                    document.querySelectorAll('.sub-menu').forEach(function(menu) {
                        if (menu !== dropdown) {
                            menu.style.display = 'none';
                        }
                    });
                    
                    // Toggle el dropdown actual
                    dropdown.style.display = isOpen ? 'none' : 'block';
                }
            }
        });
    });
    
    // Prevenir que Bootstrap interfiera con nuestro sistema
    document.addEventListener('click', function(e) {
        // Si el click es en un elemento del menú móvil, no hacer nada más
        if (e.target.closest('.left-panel') && window.innerWidth <= 768) {
            e.stopPropagation();
        }
    });
});

function toggleMobileMenu() {
    const leftPanel = document.getElementById('left-panel');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const body = document.body;
    
    if (!leftPanel || !mobileOverlay) return;
    
    const isOpen = leftPanel.classList.contains('show');
    
    // Limpiar cualquier estado inconsistente
    clearTimeout(window.menuCloseTimer);
    
    if (isOpen) {
        // Cerrar menú
        closeMobileMenu();
    } else {
        // Abrir menú
        leftPanel.classList.add('show');
        mobileOverlay.classList.add('show');
        body.classList.add('menu-open');
        
        // Asegurar que el menú esté visible
        leftPanel.style.display = 'block';
    }
}

function closeMobileMenu() {
    const leftPanel = document.getElementById('left-panel');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const body = document.body;
    
    if (!leftPanel || !mobileOverlay) return;
    
    // Remover clases de manera segura
    leftPanel.classList.remove('show');
    mobileOverlay.classList.remove('show');
    body.classList.remove('menu-open');
    
    // Cerrar todos los dropdowns
    document.querySelectorAll('.sub-menu').forEach(function(menu) {
        menu.style.display = 'none';
    });
    
    // Limpiar estilos inline que puedan interferir
    setTimeout(() => {
        if (!leftPanel.classList.contains('show')) {
            leftPanel.style.display = '';
        }
    }, 300);
}

// Mejorar la experiencia de touch
document.addEventListener('touchstart', function() {}, {passive: true});
</script>
      