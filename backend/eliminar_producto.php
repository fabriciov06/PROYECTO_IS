<?php
include("conexion.php");

// Verificamos que sí llegue el ID en la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Ejecutamos la consulta para borrar
    $sql = "DELETE FROM productos WHERE id_producto = $id";
    
    if ($conexion->query($sql) === TRUE) {
        // Si se borró con éxito, regresamos a la tabla de productos
        header("Location: ../frontend/pages/productos.php");
        exit();
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    echo "Error: No se envió ningún ID para eliminar.";
}
?>