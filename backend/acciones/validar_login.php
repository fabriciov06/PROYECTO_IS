<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';
require_once '../clases/autoload.php';

$user = $_POST['usuario'] ?? '';
$pass = $_POST['contrasenia'] ?? '';

$resultado = Usuario::login($conexion, $user, $pass);
echo json_encode($resultado);
exit();
?>