<?php
require ('f_consumo.php');
require ('dat_cws.php');

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

//obtener token
$_SESSION['token']=consumir_token(US_MABEL,PS_MABEL);
    session_start();
    if(!isset($_SESSION['token'])){ $_SESSION['token']=consumir_token(US_MABEL,PS_MABEL); }
    echo $_SESSION['token'];

//consumo de datos tiendas cod_tienda
    $cod_negocio=10;
    $res2=consumo_tiendas($_SESSION['token'],$cod_negocio);
    echo '<pre>';
    print_r($res2);
    echo '</pre>';

//consumo de datos ARQUEO DE TIENDA
    //ingreso de datos
    $cod_negocio=1;
    $cod_tienda=119                                                                                                             ;
    $num_terminal=3;
    $date='2025-04-29';

    //consumo api
    $data=consumo_arqueo_caja_date($_SESSION['token'],$cod_negocio,$cod_tienda,$num_terminal,$date);
    echo '<pre>';
    print_r($data);
    echo '</pre>';
   
    //consumo api solo formas de pago de local
    $data2=consumo_arqueo($_SESSION['token'],$cod_negocio,$cod_tienda,$num_terminal,$date);
    
    echo '<pre>';
    print_r($data2);
    echo '</pre>';




    
?>