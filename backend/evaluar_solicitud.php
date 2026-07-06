<?php
require_once 'conexion.php';

if(isset($_POST['id']) && isset($_POST['estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado']; // 'Aprobada' o 'Rechazada'
    $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : '';

    // Actualizamos el estado en la base de datos y guardamos la fecha de evaluación
    $sql = "UPDATE solicitudes_compra 
            SET estado = '$estado', 
                fecha_evaluacion = CURRENT_TIMESTAMP, 
                motivo_rechazo = '$motivo' 
            WHERE id_solicitud = $id";

    if($conexion->query($sql)) {
        echo "ok";
    } else {
        echo "error";
    }
}
?>