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
        $res = $conexion->query("SELECT DISTINCT nombre FROM categorias UNION SELECT DISTINCT categoria AS nombre FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY nombre ASC");
        $categorias = [];
        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $categorias[] = $row['nombre'];
            }
        }
        return $categorias;
    }
}
?>
