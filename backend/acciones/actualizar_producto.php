<?php
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

$id = intval($_POST['id'] ?? 0);
$nombre = $_POST['nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$stock_minimo = intval($_POST['stock_minimo'] ?? 0);
$unidad_medida = $_POST['unidad_medida'] ?? 'unidad';
$usuario = $_SESSION['usuario_logeado'] ?? 'Administrador';

$exito = Catalogo::modificarProducto($conexion, $id, $nombre, $categoria, $precio, $stock, $stock_minimo, $unidad_medida, $usuario);

if ($exito) {
    echo json_encode(['exito' => true, 'mensaje' => 'El producto se ha actualizado correctamente.']);
} else {
    echo json_encode(['exito' => false, 'error' => 'Error al actualizar el producto.']);
}
?>