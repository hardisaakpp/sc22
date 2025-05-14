<?php session_start(); if(!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] == '' || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){ session_unset(); session_destroy(); header("Location: index.php"); exit(); } ?>

<?php
    session_start();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php");
    exit;
?>