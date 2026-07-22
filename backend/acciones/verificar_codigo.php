<?php
header('Content-Type: application/json');
require_once 'conexion.php';
require_once '../clases/autoload.php';

$codigo = $_GET['codigo'] ?? '';

if (empty($codigo)) {
    echo json_encode(['estado' => 'vacio', 'mensaje' => '']);
    exit();
}

$resultado = Producto::verificarCodigo($conexion, $codigo);
echo json_encode($resultado);
?>
