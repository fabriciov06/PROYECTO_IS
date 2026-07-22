<?php
require_once __DIR__ . '/Producto.php';
require_once __DIR__ . '/Auditoria.php';

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

    public static function agregarProducto(
        mysqli $conexion,
        string $codigo,
        string $nombre,
        string $categoria,
        float $precio,
        int $stockMinimo = 0,
        int $stockInicial = 0,
        string $unidadMedida = 'unidad',
        string $descripcion = '',
        string $usuario = 'Administrador'
    ): array {
        // Sanitización (RNF-17)
        $codigoEscaped = $conexion->real_escape_string(htmlspecialchars(trim($codigo)));
        $nombreEscaped = $conexion->real_escape_string(htmlspecialchars(trim($nombre)));
        $categoriaEscaped = $conexion->real_escape_string(htmlspecialchars(trim($categoria)));
        $unidadMedidaEscaped = $conexion->real_escape_string(htmlspecialchars(trim($unidadMedida)));
        $descripcionEscaped = $conexion->real_escape_string(htmlspecialchars(trim($descripcion)));
        $estado = 'Activo';

        // Validaciones de selección obligatoria (RNF-04)
        if (empty($codigoEscaped)) {
            return ['exito' => false, 'error' => 'El código del producto es obligatorio.'];
        }
        if (empty($nombreEscaped) || mb_strlen($nombreEscaped) < 3) {
            return ['exito' => false, 'error' => 'El nombre del producto debe tener al menos 3 caracteres.'];
        }
        if (empty($categoriaEscaped)) {
            return ['exito' => false, 'error' => 'Debe seleccionar una categoría obligatoria para el producto.'];
        }
        if (empty($unidadMedidaEscaped)) {
            return ['exito' => false, 'error' => 'Debe seleccionar una unidad de medida para el producto.'];
        }

        // Validaciones numéricas estrictas (Flujo 6.2)
        if ($precio <= 0) {
            return [
                'exito' => false,
                'error' => 'El precio unitario debe ser un valor numérico mayor a S/ 0.00.'
            ];
        }
        if ($stockInicial < 0) {
            return [
                'exito' => false,
                'error' => 'El stock inicial no puede ser un valor negativo.'
            ];
        }
        if ($stockMinimo < 0) {
            return [
                'exito' => false,
                'error' => 'El stock mínimo no puede ser un valor negativo.'
            ];
        }

        // Verificación de código (Flujo 4.1 y 4.3)
        $verificacion = Producto::verificarCodigo($conexion, $codigoEscaped);
        if ($verificacion['estado'] === 'formato_invalido') {
            return ['exito' => false, 'error' => $verificacion['mensaje']];
        }
        if ($verificacion['estado'] === 'duplicado_activo') {
            return ['exito' => false, 'error' => $verificacion['mensaje']];
        }
        if ($verificacion['estado'] === 'desactivado') {
            return [
                'exito' => false,
                'es_desactivado' => true,
                'error' => $verificacion['mensaje'],
                'codigo' => $codigoEscaped
            ];
        }

        // Inserción protegida en menos de 2 segundos (RNF-12)
        $sql = "INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, unidad_medida, descripcion, estado) 
                VALUES ('$codigoEscaped', '$nombreEscaped', '$categoriaEscaped', $precio, $stockInicial, $stockMinimo, '$unidadMedidaEscaped', '$descripcionEscaped', '$estado')";

        if ($conexion->query($sql) === TRUE) {
            $idNuevo = $conexion->insert_id;
            // Auditoría automática (RNF-18)
            Auditoria::registrar(
                $conexion,
                $usuario,
                TipoOperacion::CREAR,
                'Producto',
                $idNuevo,
                "Registro de nuevo producto: $codigoEscaped - $nombreEscaped (Stock: $stockInicial, Mín: $stockMinimo, Precio: S/ $precio)"
            );
            return [
                'exito' => true,
                'mensaje' => 'El producto ha sido registrado correctamente.'
            ];
        } else {
            return [
                'exito' => false,
                'error' => 'No se pudo completar el registro: ' . ($conexion->error ?: 'Intente nuevamente.')
            ];
        }
    }

    public static function modificarProducto(
        mysqli $conexion,
        int $id,
        string $nombre,
        string $categoria,
        float $precio,
        int $stock,
        int $stockMinimo = 0,
        string $unidadMedida = 'unidad',
        string $descripcion = '',
        string $usuario = 'Administrador'
    ): bool {
        $exito = Producto::modificarProducto($conexion, $id, $nombre, $categoria, $unidadMedida, $precio, $stock, $stockMinimo, $descripcion);
        if ($exito) {
            $nombreEsc = $conexion->real_escape_string(htmlspecialchars(trim($nombre), ENT_QUOTES));
            Auditoria::registrar(
                $conexion,
                $usuario,
                TipoOperacion::MODIFICAR,
                'Producto',
                $id,
                "Modificación de producto ID: $id ($nombreEsc)"
            );
            return true;
        }
        return false;
    }

    public static function desactivarProducto(mysqli $conexion, int $id, string $usuario = 'Administrador'): bool {
        $sql = "UPDATE productos SET estado = 'Desactivado' WHERE id_producto = $id";
        if ($conexion->query($sql) === TRUE) {
            Auditoria::registrar(
                $conexion,
                $usuario,
                TipoOperacion::DESACTIVAR,
                'Producto',
                $id,
                "Desactivación lógica de producto ID: $id"
            );
            return true;
        }
        return false;
    }

    public function replicarASedes(): void {
        // Lógica
    }
}
?>
