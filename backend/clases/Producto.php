<?php
require_once __DIR__ . '/Categoria.php';
require_once __DIR__ . '/Auditoria.php';

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

    public static function verificarCodigo(mysqli $conexion, string $codigo): array {
        $codigoTrim = trim($codigo);
        
        // Validar formato (alfanumérico y guiones, entre 3 y 20 caracteres)
        if (!preg_match('/^[A-Za-z0-9\-]{3,20}$/', $codigoTrim)) {
            return [
                'estado' => 'formato_invalido',
                'mensaje' => 'El código debe respetar el formato establecido.'
            ];
        }

        $codigoEsc = $conexion->real_escape_string($codigoTrim);
        $res = $conexion->query("SELECT id_producto, nombre, estado FROM productos WHERE codigo = '$codigoEsc'");

        if ($res && $res->num_rows > 0) {
            $fila = $res->fetch_assoc();
            $estadoStr = strtolower(trim($fila['estado']));

            if ($estadoStr === 'desactivado') {
                return [
                    'estado' => 'desactivado',
                    'mensaje' => 'Este código corresponde a un producto desactivado. ¿Desea reactivarlo?',
                    'id_producto' => (int)$fila['id_producto'],
                    'nombre' => $fila['nombre']
                ];
            } else {
                return [
                    'estado' => 'duplicado_activo',
                    'mensaje' => 'El código ya está registrado. Ingrese un código diferente.'
                ];
            }
        }

        return [
            'estado' => 'disponible',
            'mensaje' => 'Código disponible.'
        ];
    }

    public static function generarSiguienteCodigo(mysqli $conexion): string {
        $res = $conexion->query("SELECT MAX(id_producto) as max_id FROM productos");
        $nextId = 1;
        if ($res && $row = $res->fetch_assoc()) {
            $nextId = ((int)$row['max_id']) + 1;
        }
        
        do {
            $codigoSugerido = 'P' . str_pad($nextId, 7, '0', STR_PAD_LEFT);
            $check = $conexion->query("SELECT id_producto FROM productos WHERE codigo = '$codigoSugerido'");
            if (!$check || $check->num_rows == 0) {
                break;
            }
            $nextId++;
        } while ($nextId < 9999999);

        return $codigoSugerido;
    }

    public static function reactivar(mysqli $conexion, string $codigo, string $usuario = 'Administrador'): bool {
        $codigoEsc = $conexion->real_escape_string(trim($codigo));
        $res = $conexion->query("SELECT id_producto, nombre FROM productos WHERE codigo = '$codigoEsc'");

        if ($res && $res->num_rows > 0) {
            $fila = $res->fetch_assoc();
            $id = (int)$fila['id_producto'];
            $nombre = $fila['nombre'];

            $sql = "UPDATE productos SET estado = 'Activo' WHERE id_producto = $id";
            if ($conexion->query($sql) === TRUE) {
                Auditoria::registrar($conexion, $usuario, TipoOperacion::MODIFICAR, 'Producto', $id, "Reactivación de producto desactivado ($codigoEsc - $nombre)");
                return true;
            }
        }
        return false;
    }

    public static function buscar(mysqli $conexion, string $q = '', string $f = 'Todos', string $min = '', string $max = '', string $cat = ''): array {
        $sql = "SELECT * FROM productos WHERE (estado != 'Desactivado' OR estado IS NULL)";

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
        $res = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE estado != 'Desactivado'");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    public static function contarStockBajo(mysqli $conexion): int {
        $res = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo AND estado != 'Desactivado'");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    public static function obtenerStockBajo(mysqli $conexion, int $limite = 5): array {
        $sql = "SELECT codigo, nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo AND estado != 'Desactivado' ORDER BY stock ASC LIMIT $limite";
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
