<?php
session_start();

// Flujo 6.1: Sesión inexistente o previamente cerrada
if (!isset($_SESSION['usuario_logeado'])) {
    header("Location: ../../frontend/pages/login.php?no_session=1");
    exit();
}

$reason = $_GET['timeout'] ?? '';

// Flujo 6: Invalida la sesión activa y destruye todos los datos
session_unset();
session_destroy();

if ($reason === '1') {
    // Flujo 3.1: Sesión expirada por inactividad
    header("Location: ../../frontend/pages/login.php?timeout=1");
} else {
    // Flujo 7: Cierre de sesión exitoso voluntario
    header("Location: ../../frontend/pages/login.php?logout=1");
}
exit();
?>