<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibir y limpiar datos (Previene ataques básicos)
    $codigo = $conexion->real_escape_string($_POST['codigo']);
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $precio = floatval($_POST['precio']);
    $stock_minimo = intval($_POST['stock_minimo']);
    $stock = 0;
    $estado = 'Agotado';

    // 2. Validaciones lógicas
    if ($precio <= 0 || $stock_minimo < 0) {
        die("Error: El precio debe ser mayor a 0 y el stock no puede ser negativo.");
    }

    // 3. Verificar si el código ya existe
    $check = $conexion->query("SELECT id_producto FROM productos WHERE codigo = '$codigo'");
    if ($check->num_rows > 0) {
        // Redirigimos con un parámetro de error
        header("Location: ../frontend/pages/productos.php?error=codigo_duplicado");
        exit();
    }

    // 4. Inserción protegida
    $sql = "INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, estado) 
            VALUES ('$codigo', '$nombre', '$categoria', '$precio', '$stock', '$stock_minimo', '$estado')";

    if ($conexion->query($sql) === TRUE) {
        header("Location: ../frontend/pages/productos.php");
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
}
?>