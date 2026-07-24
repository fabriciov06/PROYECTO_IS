<?php
header('Content-Type: application/json');
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = strip_tags(trim($_POST['codigo'] ?? ''));
    $nombre = strip_tags(trim($_POST['nombre'] ?? ''));
    $categoria = strip_tags(trim($_POST['categoria'] ?? ''));
    $precio = floatval($_POST['precio'] ?? 0);
    $stock_minimo = intval($_POST['stock_minimo'] ?? 0);
    $stock_inicial = intval($_POST['stock'] ?? 0);
    $unidad_medida = strip_tags(trim($_POST['unidad_medida'] ?? 'unidad'));
    $descripcion = strip_tags(trim($_POST['descripcion'] ?? ''));
    $usuario = $_SESSION['usuario_logeado'] ?? 'Administrador';

    $resultado = Catalogo::agregarProducto(
        $conexion,
        $codigo,
        $nombre,
        $categoria,
        $precio,
        $stock_minimo,
        $stock_inicial,
        $unidad_medida,
        $descripcion,
        $usuario
    );

    echo json_encode($resultado);
    exit();
}
?>