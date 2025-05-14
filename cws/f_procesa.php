<?php
    /*
            MABEL
        cod_negocio nombre_negocio
                9   DESCUENTON   
                2   LILI PINK
                1   ROLAND

            COSMEC
        cod_negocio nombre_negocio  
                10  CLINIQUE
                8   MAC
                11  PDPAOLA
                7   PSYCHO BUNNY
                6   SALVATORE FERRAGAMO
    */
    require ('f_consumo.php');
    require ('dat_cws.php');

    //obtener token
    session_start();
    if(!isset($_SESSION['token'])){ $_SESSION['token']=consumir_token(US_COSMEC,PS_COSMEC); }
    echo $_SESSION['token'];

    //consumo de datos tiendas cod_tienda
    $cod_negocio=10;
    $res2=consumo_tiendas($_SESSION['token'],$cod_negocio);
    echo '<pre>';
    print_r($res2);
    echo '</pre>';

    //consumo de datos arqueo_caja fechas
    $cod_negocio=10;
    $cod_tienda=173;
    $num_terminal=1;
    $date='2025-04-20';
    $resultado=consumo_arqueo_caja_date($_SESSION['token'],$cod_negocio,$cod_tienda,$num_terminal,$date);
    echo '<pre>';
    print_r($resultado);
    echo '</pre>';
?>