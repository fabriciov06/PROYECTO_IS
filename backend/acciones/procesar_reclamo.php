<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

if (isset($_POST['id']) && isset($_POST['solucion']) && isset($_POST['estado'])) {
    $id = intval($_POST['id']);
    $solucion = $_POST['solucion'];
    $estado = $_POST['estado'];

    $exito = Proveedor::cargarSolucionReclamo($conexion, $id, $solucion, $estado);
    echo $exito ? "ok" : "error";
}
?>