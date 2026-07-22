<?php
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

$id = intval($_REQUEST['id'] ?? 0);
$usuario = $_SESSION['usuario_logeado'] ?? 'Administrador';

if ($id > 0 && Catalogo::desactivarProducto($conexion, $id, $usuario)) {
    echo json_encode(['exito' => true, 'mensaje' => 'El producto ha sido desactivado correctamente.']);
} else {
    echo json_encode(['exito' => false, 'error' => 'Error al desactivar el producto.']);
}
?>