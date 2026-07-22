<?php
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

$codigo = $_POST['codigo'] ?? '';
$usuario = $_SESSION['usuario_logeado'] ?? 'Administrador';

if (empty($codigo)) {
    echo json_encode(['exito' => false, 'error' => 'Código no proporcionado']);
    exit();
}

$exito = Producto::reactivar($conexion, $codigo, $usuario);

if ($exito) {
    echo json_encode(['exito' => true, 'mensaje' => 'El producto ha sido reactivado correctamente.']);
} else {
    echo json_encode(['exito' => false, 'error' => 'No se pudo reactivar el producto.']);
}
?>
