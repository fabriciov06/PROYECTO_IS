<?php
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

$user = $_POST['usuario'] ?? '';
$pass = $_POST['contrasenia'] ?? '';

if (Usuario::login($conexion, $user, $pass)) {
    header("Location: ../../frontend/pages/productos.php");
    exit();
} else {
    header("Location: ../../frontend/pages/login.php?error=1");
    exit();
}
?>