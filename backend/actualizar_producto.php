<?php
include("conexion.php");

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];

$sql = "UPDATE productos SET nombre='$nombre', precio='$precio', stock='$stock' WHERE id_producto = $id";

if ($conexion->query($sql) === TRUE) {
    header("Location: ../frontend/pages/productos.php");
} else {
    echo "Error al actualizar: " . $conexion->error;
}
?>