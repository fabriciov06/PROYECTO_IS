<?php
require_once __DIR__ . '/Categoria.php';

class Producto {
    private int $idProducto;
    private string $codigo;
    private string $nombre;
    private string $descripcion;
    private string $unidadMedida;
    private float $precioReferencial;
    private int $stockMinimo;
    private bool $estado;
    private Categoria $categoria;

    public function __construct(int $idProducto, string $codigo, string $nombre, string $descripcion, string $unidadMedida, float $precioReferencial, int $stockMinimo, bool $estado, Categoria $categoria) {
        $this->idProducto = $idProducto;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->unidadMedida = $unidadMedida;
        $this->precioReferencial = $precioReferencial;
        $this->stockMinimo = $stockMinimo;
        $this->estado = $estado;
        $this->categoria = $categoria;
    }

    public static function buscar(mysqli $conexion, string $q = '', string $f = 'Todos', string $min = '', string $max = '', string $cat = ''): array {
        $sql = "SELECT * FROM productos WHERE 1=1";

        if ($q != '') {
            $qEsc = $conexion->real_escape_string($q);
            $sql .= " AND (nombre LIKE '%$qEsc%' OR codigo LIKE '%$qEsc%')";
        }
        if ($f == 'Stock Bajo') {
            $sql .= " AND stock < stock_minimo AND stock > 0";
        } elseif ($f == 'Agotados') {
            $sql .= " AND stock <= 0";
        }
        if ($min != '') {
            $sql .= " AND precio >= " . floatval($min);
        }
        if ($max != '') {
            $sql .= " AND precio <= " . floatval($max);
        }
        if ($cat != '') {
            $catEsc = $conexion->real_escape_string($cat);
            $sql .= " AND categoria = '$catEsc'";
        }

        $sql .= " ORDER BY codigo ASC";
        $resultado = $conexion->query($sql);

        $productos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $productos[] = $fila;
            }
        }
        return $productos;
    }

    public static function contarTotal(mysqli $conexion): int {
        $res = $conexion->query("SELECT COUNT(*) as total FROM productos");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    public static function contarStockBajo(mysqli $conexion): int {
        $res = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    public static function obtenerStockBajo(mysqli $conexion, int $limite = 5): array {
        $sql = "SELECT codigo, nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo ORDER BY stock ASC LIMIT $limite";
        $res = $conexion->query($sql);
        $filas = [];
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $filas[] = $fila;
            }
        }
        return $filas;
    }

    public function actualizarEstado(bool $nuevoEstado): void {
        $this->estado = $nuevoEstado;
    }
}
?>
