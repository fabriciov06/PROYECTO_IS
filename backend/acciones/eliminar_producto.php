<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if (Catalogo::desactivarProducto($conexion, $id)) {
        header("Location: ../../frontend/pages/productos.php");
        exit();
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    echo "Error: No se envió ningún ID para eliminar.";
}
?>