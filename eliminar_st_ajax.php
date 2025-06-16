<?php
    session_start();
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clave'])) {
        $clave = $_POST['clave'];

        if (isset($_SESSION['sts'][$clave])) {
            unset($_SESSION['sts'][$clave]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false]);
?>