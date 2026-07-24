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
        $res = self::buscarPaginado($conexion, $q, $f, $min, $max, $cat, 1, 1000);
        return $res['productos'];
    }

    public static function buscarPaginado(
        mysqli $conexion,
        string $q = '',
        string $f = 'Todos',
        string $min = '',
        string $max = '',
        string $cat = '',
        int $pagina = 1,
        int $limite = 25
    ): array {
        // Flujo Alterno 4.2: Búsqueda muy corta (< 2 caracteres), no filtrar por $q
        $qClean = trim($q);
        if (mb_strlen($qClean, 'UTF-8') < 2) {
            $qClean = '';
        }

        // Sanitización de $limite (permitidos: 10, 25, 50, 100)
        $limitesValidos = [10, 25, 50, 100];
        if (!in_array($limite, $limitesValidos, true)) {
            $limite = 25;
        }

        $baseWhere = " WHERE (estado != 'Desactivado' OR estado IS NULL)";
        $tipos = '';
        $parametros = [];

        if ($qClean !== '') {
            $baseWhere .= " AND (nombre LIKE ? OR codigo LIKE ?)";
            $like = '%' . $qClean . '%';
            $tipos .= 'ss';
            $parametros[] = $like;
            $parametros[] = $like;
        }

        // Estado de stock
        if ($f == 'Stock Bajo') {
            $baseWhere .= " AND stock < stock_minimo AND stock > 0";
        } elseif ($f == 'Agotados' || $f == 'Agotado') {
            $baseWhere .= " AND stock <= 0";
        } elseif ($f == 'Normal') {
            $baseWhere .= " AND stock >= stock_minimo AND stock > 0";
        }

        if ($min !== '') {
            $baseWhere .= " AND precio >= ?";
            $tipos .= 'd';
            $parametros[] = floatval($min);
        }
        if ($max !== '') {
            $baseWhere .= " AND precio <= ?";
            $tipos .= 'd';
            $parametros[] = floatval($max);
        }
        if ($cat !== '') {
            $baseWhere .= " AND categoria = ?";
            $tipos .= 's';
            $parametros[] = $cat;
        }

        // 1. OBTENER CONTEO TOTAL GLOBAL (Paso 4 / RNF-11)
        $sqlCount = "SELECT COUNT(*) as total FROM productos" . $baseWhere;
        $stmtCount = $conexion->prepare($sqlCount);
        $total = 0;
        if ($stmtCount) {
            if ($tipos !== '') {
                $stmtCount->bind_param($tipos, ...$parametros);
            }
            if ($stmtCount->execute()) {
                $resCount = $stmtCount->get_result();
                if ($resCount && $rowC = $resCount->fetch_assoc()) {
                    $total = (int)$rowC['total'];
                }
                if ($resCount) $resCount->free();
            }
            $stmtCount->close();
        }

        // 2. CALCULAR PÁGINAS Y AJUSTAR PÁGINA (Flujo 8.1 - Página fuera de rango)
        $totalPaginas = $total > 0 ? (int)ceil($total / $limite) : 1;
        if ($pagina < 1) {
            $pagina = 1;
        }
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
        }

        $offset = ($pagina - 1) * $limite;

        // 3. CONSULTAR PRODUCTOS DE LA PÁGINA ACTUAL (Ordenados por código ASC RNF-09)
        $sqlSelect = "SELECT * FROM productos" . $baseWhere . " ORDER BY codigo ASC LIMIT ? OFFSET ?";
        $tiposSelect = $tipos . 'ii';
        $parametrosSelect = array_merge($parametros, [$limite, $offset]);

        $productos = [];
        $stmtSelect = $conexion->prepare($sqlSelect);
        if ($stmtSelect) {
            if ($stmtSelect->bind_param($tiposSelect, ...$parametrosSelect)) {
                if ($stmtSelect->execute()) {
                    $resSel = $stmtSelect->get_result();
                    if ($resSel) {
                        while ($row = $resSel->fetch_assoc()) {
                            $productos[] = $row;
                        }
                        $resSel->free();
                    }
                }
            }
            $stmtSelect->close();
        }

        $desde = $total > 0 ? $offset + 1 : 0;
        $hasta = $total > 0 ? min($offset + $limite, $total) : 0;

        return [
            'productos' => $productos,
            'total' => $total,
            'pagina_actual' => $pagina,
            'total_paginas' => $totalPaginas,
            'limite' => $limite,
            'desde' => $desde,
            'hasta' => $hasta
        ];
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

        try {
            // Verificar si la tabla de solicitudes existe en la BD actual
            $checkSol = $conexion->query("SHOW TABLES LIKE 'detalle_solicitud'");
            if ($checkSol && $checkSol->num_rows > 0) {
                $sqlSolicitudes = "
                    SELECT COUNT(*) AS total
                    FROM detalle_solicitud ds
                    INNER JOIN solicitudes_compra sc
                        ON ds.id_solicitud = sc.id_solicitud
                    WHERE ds.id_producto = ?
                      AND sc.estado = 'Pendiente'
                ";

                $stmtSol = $conexion->prepare($sqlSolicitudes);
                if ($stmtSol) {
                    if ($stmtSol->bind_param("i", $id) && $stmtSol->execute()) {
                        $resSol = $stmtSol->get_result();
                        if ($resSol) {
                            $rowSol = $resSol->fetch_assoc();
                            $totalSol = $rowSol ? (int)$rowSol['total'] : 0;
                            $resSol->free();
                            $stmtSol->close();
                            if ($totalSol > 0) {
                                return true;
                            }
                        } else {
                            $stmtSol->close();
                        }
                    } else {
                        $stmtSol->close();
                    }
                }
            }

            // Verificar si la tabla de informes existe en la BD actual
            $checkInf = $conexion->query("SHOW TABLES LIKE 'detalle_informe'");
            if ($checkInf && $checkInf->num_rows > 0) {
                $sqlInformes = "
                    SELECT COUNT(*) AS total
                    FROM detalle_informe di
                    INNER JOIN informe_recepcion ir
                        ON di.id_informe = ir.id_informe
                    WHERE di.id_producto = ?
                      AND ir.estado = 'Pendiente'
                ";

                $stmtInf = $conexion->prepare($sqlInformes);
                if ($stmtInf) {
                    if ($stmtInf->bind_param("i", $id) && $stmtInf->execute()) {
                        $resInf = $stmtInf->get_result();
                        if ($resInf) {
                            $rowInf = $resInf->fetch_assoc();
                            $totalInf = $rowInf ? (int)$rowInf['total'] : 0;
                            $resInf->free();
                            $stmtInf->close();
                            if ($totalInf > 0) {
                                return true;
                            }
                        } else {
                            $stmtInf->close();
                        }
                    } else {
                        $stmtInf->close();
                    }
                }
            }
        } catch (Throwable $e) {
            return false;
        }

        return false;
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
        $nombreEscaped = $conexion->real_escape_string(htmlspecialchars(strip_tags(trim($nombre)), ENT_QUOTES));
        $categoriaEscaped = $conexion->real_escape_string(htmlspecialchars(strip_tags(trim($categoria)), ENT_QUOTES));
        $unidadMedidaEscaped = $conexion->real_escape_string(htmlspecialchars(strip_tags(trim($unidadMedida)), ENT_QUOTES));
        $descripcionEscaped = $conexion->real_escape_string(htmlspecialchars(strip_tags(trim($descripcion)), ENT_QUOTES));

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
