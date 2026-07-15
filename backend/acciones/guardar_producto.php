<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $precio = floatval($_POST['precio'] ?? 0);
    $stock_minimo = intval($_POST['stock_minimo'] ?? 0);

    $resultado = Catalogo::agregarProducto($conexion, $codigo, $nombre, $categoria, $precio, $stock_minimo);

    if ($resultado['exito']) {
        header("Location: ../../frontend/pages/productos.php");
    } else {
        if ($resultado['error'] === 'codigo_duplicado') {
            header("Location: ../../frontend/pages/productos.php?error=codigo_duplicado");
        } else {
            echo "Error al guardar: " . $resultado['error'];
        }
    }
}
?>