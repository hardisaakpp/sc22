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
        //INVENTARIOS TFA
                    if ($userAdmin==1 && $userName <> 'SISTEMAS') { // ADMIN   
                        echo '
                        <li class="menu-item-has-children dropdown">
                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-spinner"></i><a href="loadTF.php">Cargar tomas aleatorias</a></li>
                            <li><i class="fa fa-h-square"></i><a href="tfaL.php">Revisar tomas tiendas</a></li>
                        </ul>
                        </li>
                        ';
                    } else   if ( $userName === 'SISTEMAS') { // ADMIN   
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
                    } else if ($userAdmin==2){ // TIENDA
                        echo '
                        <li class="menu-item-has-children dropdown">
                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-pencil-square-o"></i><a href="tfaD.php">Toma aleatoria</a></li>
                            <li><i class="fa fa-h-square"></i><a href="tfaHu.php">Historial</a></li>
                        </ul>
                        </li>
                        ';
                    } else if ($userAdmin==3){ // INVENTARIO
                        echo '
                        <li class="menu-item-has-children dropdown">
                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check-square"></i>Inventarios</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-spinner"></i><a href="loadTF.php">Cargar tomas aleatorias</a></li>
                            <li><i class="fa fa-h-square"></i><a href="tfaL.php">Revisar tomas tiendas</a></li>
                        </ul>
                        </li>
                        ';
                    }
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


//reposicion tienda
                    if ($userName == 'RL-PSC' || strpos($userName, 'OUT') === 0) {
                        
                        
                        echo '                       
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-shopping-cart"></i>Reposiciones</a>
                                <ul class="sub-menu children dropdown-menu">     
                                    <li><i class="ti ti-menu-alt"></i><a href="repC.php">Solicitar</a></li>

                                    <li><i class="ti ti-shopping-cart"></i><a href="repS.php">Carrito</a></li>
                                   

                                    <li><i class="fa fa-h-square"></i><a href="repH.php">Historial</a></li>
                                
                                </ul>
                            </li>
                        ';
                        }



        //PRESUPUESTOS - METAS
                        $LP = "LP"; $SAL = "SAL";
                        $RL = "RL";
                        $OUT = "OUT";
                        $YHD = "YHD";

                            if ($userAdmin==1 ) { // ADMIN   
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
                            }else if ($userAdmin==2){ // TIENDA

                                if ( substr($userName, 0, strlen($RL)) === $RL || substr($userName, 0, strlen($OUT)) === $OUT || substr($userName, 0, strlen($YHD)) === $YHD  ) {
                                    echo '<li class="menu-item-has-children dropdown">
                                    <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                    <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-spinner"></i><a href="prevenImpRLt.php">Cargar</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLt.php">Revisar Metas</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLmDAY.php">Ventas Día/Asesor</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLm.php">Cumplimiento</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenList.php">Maestro Vendedores</a></li>
                                     </ul>
                                </li>';
                                } else   if (substr($userName, 0, strlen($LP)) === $LP  ) {
                                    echo '<li class="menu-item-has-children dropdown">
                                    <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                    <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-spinner"></i><a href="prevenImpRLt.php">Cargar</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLt.php">Revisar Metas</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLmDAY.php">Ventas Día/Asesor</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLm.php">Cumplimiento</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenList.php">Maestro Vendedores</a></li>       
                                    </ul>
                                </li>';
                                        } else {
                                            echo '
                                            <li class="menu-item-has-children dropdown">
                                <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-spinner"></i><a href="prevenImp.php">Cargar COSMEC</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenL.php">Revisar COSMEC</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenLRLmDAY.php">Ventas Día/Asesor</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLRLm.php">Cumplimiento</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenLIstC.php">Maestro Vendedores COSMEC</a></li>
                                            </ul>
                                </li>';
                                        
                                }
                               




                            }  else if ($userId==274){ // TIENDA
                                echo '
                                <li class="menu-item-has-children dropdown">
                                <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                <ul class="sub-menu children dropdown-menu">
                                  
                                    <li><i class="fa fa-spinner"></i><a href="prevenImpLP.php">Cargar LILI PINK</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLLP.php">Revisar LILI PINK</a></li>
                                    <li><i class="fa fa-h-square"></i><a href="prevenLIstC.php">Maestro Vendedores</a></li>
                                </ul>
                                </li>
                                ';
                            } else if ($userId==275){ // TIENDA
                                echo '
                                <li class="menu-item-has-children dropdown">
                                <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                <ul class="sub-menu children dropdown-menu">
                                  
                                <li><i class="fa fa-spinner"></i><a href="prevenImpRL.php">Cargar ROLAND</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenLRL.php">Revisar ROLAND</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenLIstC.php">Maestro Vendedores</a></li>
                                </ul>
                                </li>
                                ';
                            } else if ($userId==276){ // TIENDA
                                echo '
                                <li class="menu-item-has-children dropdown">
                                <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-dashboard"></i>Presupuesto Vendedor</a>
                                <ul class="sub-menu children dropdown-menu">
                                  
                                <li><i class="fa fa-spinner"></i><a href="prevenImp.php">Cargar COSMEC</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenL.php">Revisar COSMEC</a></li>
                                <li><i class="fa fa-h-square"></i><a href="prevenLIstC.php">Maestro Vendedores</a></li>
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
        //CIERRES DE CAJA-->
                    if ($userAdmin==1) { // ADMIN   
                        echo '
                        <li class="menu-item-has-children dropdown">
                        <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-pencil-square-o"></i><a href="cicL.php">Cierres de caja (HITELL)</a></li>
                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaL.php">Cierres de caja (MT)</a></li>
                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaLlp.php">Cierres de caja (LP-HITELL)</a></li>
                            <li><i class="fa fa-pencil-square-o"></i><a href="cicaLce.php">Cierres de caja (CE)</a></li>
                            <li><i class="fa fa-pencil-square-o"></i><a href="depL.php">DEPOSITOS [Tiendas]</a></li>
                            <li><i class="fa fa-pencil-square-o"></i><a href="depLa.php">DEPOSITOS POR TIENDA DIA [Contabilidad]</a></li>
                            <li><i class="fa fa-pencil-square-o"></i><a href="depLa2.php">DEPOSITOS [Contabilidad]</a></li>
                        </ul>
                        </li>
                        ';
                    } else if ($userAdmin==2){ // TIENDA
                        echo '
                            <li class="menu-item-has-children dropdown">
                            <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                            <ul class="sub-menu children dropdown-menu">
                                 <li><i class="fa fa-pencil-square-o"></i><a href="cic2.php">Cierre de Caja</a></li>
                                 <li><i class="fa fa-h-square"></i><a href="cic2H.php">Historial</a></li>
                                 
                            ';
                                 if ($_SESSION["emp"] == 'MT') {
                                    ?>

                                        <li><i class="fa fa-money"></i><a href="depL.php">Depositos</a></li>
                                    </ul>
                                    </li>

                                      
                                    <?php
                                } else {
                                    ?>
                                        <li><i class="fa fa-money"></i><a href="depLce.php">Depositos</a></li>
                                    </ul>
                                    </li>
                                    <?php
                                }
                                    
                    }
                        
        // CIERRES DE CAJA PARA CONTABILIDAD               
                if ($userAdmin==6 && $userName=='CONTABILIDADMT'){ 
                    echo '
                    <li class="menu-item-has-children dropdown">
                    <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                    <ul class="sub-menu children dropdown-menu">
                        <li><i class="fa fa-pencil-square-o"></i><a href="cicL.php">Cierres de caja</a></li>
                        <li><i class="fa fa-refresh"></i><a href="cic2f5.php">Actualiza cajas por día [SAP]</a></li>
                        <li><i class="fa fa-money"></i><a href="depLa.php">Dépositos Día-Tienda</a></li>
                        <li><i class="fa fa-money"></i><a href="depLa2.php">Dépositos Detalle</a></li>
                    </ul>
                    </li>

                    
                    ';
                }else if ($userAdmin==6 && $userName=='CONTABILIDADCE'){ // INVENTARIO
                    echo '
                    <li class="menu-item-has-children dropdown">
                    <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Contabilidad</a>
                    <ul class="sub-menu children dropdown-menu">
                        <li><i class="fa fa-pencil-square-o"></i><a href="cicaLce.php">Cierres de caja (CE)</a></li>
                        <li><i class="fa fa-pencil-square-o"></i><a href="cicLce.php">Cierres de caja (CE-HITELL)</a></li>
                        <li><i class="fa fa-refresh"></i><a href="cic2f5.php">Actualiza cajas por día [SAP]</a></li>
                    </ul>
                    </li>
                    ';
                }
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
                        if ($userAdmin==1 ) {   
                        
                        
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
                        }

            //MIGRACION       
                        if ($userAdmin==1 ) {   
                        
                        
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
                        }
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
                                </ul>
                            </li>
                        ';
                        }
        //CEDI 2.0                
                        if ($userAdmin==1 ) {   
                        
                        
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
      