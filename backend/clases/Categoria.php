<?php
class Categoria {
    private int $idCategoria;
    private string $nombre;
    private string $descripcion;

    public function __construct(int $idCategoria, string $nombre, string $descripcion) {
        $this->idCategoria = $idCategoria;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    public static function listar(mysqli $conexion): array {
        $categorias = [];

        // 1. Consultar de la tabla `categorias` si existe en la base de datos
        try {
            $checkTable = $conexion->query("SHOW TABLES LIKE 'categorias'");
            if ($checkTable && $checkTable->num_rows > 0) {
                $res = $conexion->query("SELECT DISTINCT nombre FROM categorias WHERE nombre IS NOT NULL AND nombre != ''");
                if ($res && $res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $categorias[] = $row['nombre'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignorar error si la tabla no existe
        }

        // 2. Consultar categorías activas en la tabla `productos`
        try {
            $resProd = $conexion->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != ''");
            if ($resProd && $resProd->num_rows > 0) {
                while ($row = $resProd->fetch_assoc()) {
                    if (!in_array($row['categoria'], $categorias)) {
                        $categorias[] = $row['categoria'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignorar
        }

        // 3. Fallback con categorías estándar de Tiendas MASS si la base de datos está vacía
        if (empty($categorias)) {
            $categorias = ['Abarrotes', 'Bebidas', 'Cuidado Personal', 'Embutidos', 'Lácteos', 'Limpieza', 'Panadería', 'Snacks'];
        }

        sort($categorias);
        return array_values(array_unique($categorias));
    }
}
?>
