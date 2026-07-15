<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

if (isset($_POST['id']) && isset($_POST['estado'])) {
    $id = intval($_POST['id']);
    $estado = $_POST['estado'];

    $exito = Operador::enviarInforme($conexion, $id, $estado);
    echo $exito ? "ok" : "error";
}
?>