<?php
require_once __DIR__ . '/Producto.php';

class Catalogo {
    private int $idCatalogo;
    private string $sede;
    private string $fechaActualizacion;
    private array $productos = [];

    public function __construct(int $idCatalogo, string $sede, string $fechaActualizacion) {
        $this->idCatalogo = $idCatalogo;
        $this->sede = $sede;
        $this->fechaActualizacion = $fechaActualizacion;
    }

    public static function agregarProducto(mysqli $conexion, string $codigo, string $nombre, string $categoria, float $precio, int $stockMinimo): array {
        $codigoEscaped = $conexion->real_escape_string($codigo);
        $nombreEscaped = $conexion->real_escape_string($nombre);
        $categoriaEscaped = $conexion->real_escape_string($categoria);
        $stock = 0;
        $estado = 'Agotado';

        if ($precio <= 0 || $stockMinimo < 0) {
            return ['exito' => false, 'error' => 'El precio debe ser mayor a 0 y el stock no puede ser negativo.'];
        }

        $check = $conexion->query("SELECT id_producto FROM productos WHERE codigo = '$codigoEscaped'");
        if ($check && $check->num_rows > 0) {
            return ['exito' => false, 'error' => 'codigo_duplicado'];
        }

        $sql = "INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, estado) 
                VALUES ('$codigoEscaped', '$nombreEscaped', '$categoriaEscaped', '$precio', '$stock', '$stockMinimo', '$estado')";

        if ($conexion->query($sql) === TRUE) {
            return ['exito' => true];
        } else {
            return ['exito' => false, 'error' => $conexion->error];
        }
    }

    public static function modificarProducto(mysqli $conexion, int $id, string $nombre, float $precio, int $stock): bool {
        $nombreEscaped = $conexion->real_escape_string($nombre);
        $sql = "UPDATE productos SET nombre='$nombreEscaped', precio='$precio', stock='$stock' WHERE id_producto = $id";
        return $conexion->query($sql) === TRUE;
    }

    public static function desactivarProducto(mysqli $conexion, int $id): bool {
        $sql = "DELETE FROM productos WHERE id_producto = $id";
        return $conexion->query($sql) === TRUE;
    }

    public function replicarASedes(): void {
        // Lógica
    }
}
?>
