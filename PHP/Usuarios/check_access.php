<?php
function checkAccess($required_roles) {
    session_start();
    
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: /comedor/unauthorized.php');
        exit();
    }

    if (!in_array($_SESSION['role_id'], $required_roles)) {
        header('Location: /comedor/unauthorized.php');
        exit();
    }
}
?>
