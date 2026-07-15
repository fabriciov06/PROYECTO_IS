<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

$id = intval($_POST['id'] ?? 0);
$nombre = $_POST['nombre'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);

if (Catalogo::modificarProducto($conexion, $id, $nombre, $precio, $stock)) {
    header("Location: ../../frontend/pages/productos.php");
} else {
    echo "Error al actualizar: " . $conexion->error;
}
?>