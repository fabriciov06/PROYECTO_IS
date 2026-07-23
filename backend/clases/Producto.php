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

    public static function obtenerPorId(mysqli $conexion, int $id): ?array {
        $id = intval($id);

        $stmt = $conexion->prepare(
            "SELECT * FROM productos WHERE id_producto = ?"
        );

        if (!$stmt) {
            throw new Exception(
                "Error al preparar la consulta de selección del producto."
            );
        }

        if (
            !$stmt->bind_param("i", $id) ||
            !$stmt->execute()
        ) {
            $stmt->close();

            throw new Exception(
                "Error al ejecutar la consulta de selección del producto."
            );
        }

        $res = $stmt->get_result();

        if (!$res) {
            $stmt->close();

            throw new Exception(
                "Error al obtener los resultados del producto."
            );
        }

        $producto = null;

        if ($res->num_rows > 0) {
            $producto = $res->fetch_assoc();
        }

        $res->free();
        $stmt->close();

        return $producto;
    }

    public static function tieneOperacionesPendientes(
        mysqli $conexion,
        int $id
    ): bool {
        $id = intval($id);

        $sqlSolicitudes = "
            SELECT COUNT(*) AS total
            FROM detalle_solicitud ds
            INNER JOIN solicitudes_compra sc
                ON ds.id_solicitud = sc.id_solicitud
            WHERE ds.id_producto = ?
              AND sc.estado = 'Pendiente'
        ";

        $stmtSol = $conexion->prepare($sqlSolicitudes);

        if (!$stmtSol) {
            throw new Exception(
                "Error al preparar la consulta de solicitudes de compra pendientes."
            );
        }

        if (
            !$stmtSol->bind_param("i", $id) ||
            !$stmtSol->execute()
        ) {
            $stmtSol->close();

            throw new Exception(
                "Error al ejecutar la consulta de solicitudes de compra pendientes."
            );
        }

        $resSol = $stmtSol->get_result();

        if (!$resSol) {
            $stmtSol->close();

            throw new Exception(
                "Error al obtener resultados de solicitudes de compra pendientes."
            );
        }

        $rowSol = $resSol->fetch_assoc();
        $totalSol = $rowSol ? (int)$rowSol['total'] : 0;

        $resSol->free();
        $stmtSol->close();

        if ($totalSol > 0) {
            return true;
        }

        $sqlInformes = "
            SELECT COUNT(*) AS total
            FROM detalle_informe di
            INNER JOIN informe_recepcion ir
                ON di.id_informe = ir.id_informe
            WHERE di.id_producto = ?
              AND ir.estado = 'Pendiente'
        ";

        $stmtInf = $conexion->prepare($sqlInformes);

        if (!$stmtInf) {
            throw new Exception(
                "Error al preparar la consulta de informes de recepción pendientes."
            );
        }

        if (
            !$stmtInf->bind_param("i", $id) ||
            !$stmtInf->execute()
        ) {
            $stmtInf->close();

            throw new Exception(
                "Error al ejecutar la consulta de informes de recepción pendientes."
            );
        }

        $resInf = $stmtInf->get_result();

        if (!$resInf) {
            $stmtInf->close();

            throw new Exception(
                "Error al obtener resultados de informes de recepción pendientes."
            );
        }

        $rowInf = $resInf->fetch_assoc();
        $totalInf = $rowInf ? (int)$rowInf['total'] : 0;

        $resInf->free();
        $stmtInf->close();

        return $totalInf > 0;
    }

    public static function modificarProducto(
        mysqli $conexion,
        int $id,
        string $nombre,
        string $categoria,
        string $unidadMedida,
        float $precio,
        int $stock,
        int $stockMinimo,
        string $descripcion = ''
    ): bool {
        $id = intval($id);
        $nombreEscaped = $conexion->real_escape_string(htmlspecialchars(trim($nombre), ENT_QUOTES));
        $categoriaEscaped = $conexion->real_escape_string(htmlspecialchars(trim($categoria), ENT_QUOTES));
        $unidadMedidaEscaped = $conexion->real_escape_string(htmlspecialchars(trim($unidadMedida), ENT_QUOTES));
        $descripcionEscaped = $conexion->real_escape_string(htmlspecialchars(trim($descripcion), ENT_QUOTES));

        $sql = "UPDATE productos SET 
                    nombre = '$nombreEscaped', 
                    categoria = '$categoriaEscaped', 
                    unidad_medida = '$unidadMedidaEscaped', 
                    precio = $precio, 
                    stock = $stock, 
                    stock_minimo = $stockMinimo, 
                    descripcion = '$descripcionEscaped'
                WHERE id_producto = $id";

        return $conexion->query($sql) === TRUE;
    }
}
?>
