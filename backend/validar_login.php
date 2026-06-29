<?php
session_start();
include 'conexion.php';

$user = $_POST['usuario'];
$pass = $_POST['contrasenia'];

// Consulta preparada para evitar inyecciones SQL
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contrasenia = ?");
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $_SESSION['usuario_logeado'] = $fila['nombre'];
    header("Location: ../frontend/pages/productos.php");
    exit();
} else {
    header("Location: ../frontend/pages/login.php?error=1");
    exit();
}
?>