<?php
    session_start();
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'eliminar') {
        unset($_SESSION['sts']);
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false]);
?>