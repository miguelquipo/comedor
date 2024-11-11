<?php
session_start();
include '../db.php'; // Incluir archivo de conexión a la base de datos si es necesario

// Destruir las variables de sesión
$_SESSION = array();

// Destruir la sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Destruir la sesión
session_destroy();

// Actualizar el estado de la sesión en la base de datos
if (isset($_SESSION['user_id'])) {
    sqlsrv_query($conn, "UPDATE Usuarios SET session_active = 0 WHERE id = ?", array($_SESSION['user_id']));
}

// Redirigir al usuario a la página de login o a la página principal
header("Location: /comedor/PHP/Usuarios/login.php");
exit;
?>
