<?php
header('Content-Type: application/json');
require_once 'conexion.php';
require_once '../clases/autoload.php';

$categorias = Categoria::listar($conexion);
echo json_encode($categorias);
?>
