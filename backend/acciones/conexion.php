<?php
// Datos de configuración de XAMPP
$servidor = "localhost";
$usuario = "root";
$clave = "";
$base_datos = "inventario_mass";

// Intentamos conectar
$conexion = new mysqli($servidor, $usuario, $clave, $base_datos);

// Verificamos si hay error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Esto es para que acepte tildes y ñ sin problemas
$conexion->set_charset("utf8");
?>