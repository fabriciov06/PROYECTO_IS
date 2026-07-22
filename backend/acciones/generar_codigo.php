<?php
header('Content-Type: application/json');
require_once 'conexion.php';
require_once '../clases/autoload.php';

$codigo = Producto::generarSiguienteCodigo($conexion);
echo json_encode(['codigo' => $codigo]);
?>
