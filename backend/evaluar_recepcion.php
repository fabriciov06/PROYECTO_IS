<?php
require_once 'conexion.php';

if(isset($_POST['id']) && isset($_POST['estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado']; // 'Conforme' o 'Con Incidencias'

    // Actualizamos el estado del informe en la base de datos
    $sql = "UPDATE informe_recepcion 
            SET estado = '$estado' 
            WHERE id_informe = $id";

    if($conexion->query($sql)) {
        echo "ok";
    } else {
        echo "error";
    }
}
?>