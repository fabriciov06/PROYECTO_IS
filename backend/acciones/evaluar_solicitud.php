<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

if (isset($_POST['id']) && isset($_POST['estado'])) {
    $id = intval($_POST['id']);
    $estado = $_POST['estado'];
    $motivo = $_POST['motivo'] ?? '';

    if ($estado === 'Aprobada') {
        $exito = Administrador::aprobarSolicitud($conexion, $id);
    } else {
        $exito = Administrador::rechazarSolicitud($conexion, $id, $motivo);
    }

    echo $exito ? "ok" : "error";
}
?>