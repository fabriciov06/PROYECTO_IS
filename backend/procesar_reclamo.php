<?php
require_once 'conexion.php';

if (isset($_POST['id']) && isset($_POST['solucion']) && isset($_POST['estado'])) {
    $id = $_POST['id'];
    $solucion = $_POST['solucion'];
    $estado = $_POST['estado'];

    // Limpieza básica de strings para evitar errores de sintaxis en la consulta
    $solucion = str_replace("'", "\'", $solucion);

    $sql = "UPDATE reclamos 
            SET solucion_proveedor = '$solucion', 
                estado = '$estado' 
            WHERE id_reclamo = $id";

    if ($conexion->query($sql)) {
        echo "ok";
    } else {
        echo "error";
    }
}
?>