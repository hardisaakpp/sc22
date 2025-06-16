<?php
    session_start();
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])){
        if(!isset($_SESSION['sts'])){ $_SESSION['sts'] = []; }
        if(!isset($_SESSION['st'])){ $_SESSION['st'] = []; }
        foreach ($_POST['search'] as $key => $data) {
            if (isset($data['activo']) && $data['activo'] === '1') {
                $_SESSION['sts'][$key] = 2; 
                $_SESSION['st'][] = [
                    'DocNum'        => $data['DocNum'],
                    'nombre'        => $data['nombre'],
                    'LineStatus'    => $data['LineStatus'],
                    'DocDate'       => $data['DocDate'],
                    'Comments'      => $data['Comments'],
                    'odc'           => $data['query'],
                ];
            }
        }
        $_POST = [];
        header('Location: buscar_st.php');
        exit;
    }
?>