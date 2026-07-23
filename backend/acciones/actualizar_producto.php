<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../clases/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['exito' => false, 'error' => 'Método no permitido.']);
    exit();
}

if (!isset($_SESSION['usuario_logeado'])) {
    echo json_encode(['exito' => false, 'error' => 'Su sesión ha finalizado. Vuelva a iniciar sesión.']);
    exit();
}

$usuario = $_SESSION['usuario_logeado'] ?? 'Administrador';

// Recepción de parámetros POST
$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$unidad_medida = trim($_POST['unidad_medida'] ?? 'unidad');
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$stock_minimo = intval($_POST['stock_minimo'] ?? 0);
$descripcion = trim($_POST['descripcion'] ?? '');

// Sanitización (RNF-17)
$nombreEscaped = htmlspecialchars($nombre, ENT_QUOTES);
$categoriaEscaped = htmlspecialchars($categoria, ENT_QUOTES);
$unidadMedidaEscaped = htmlspecialchars($unidad_medida, ENT_QUOTES);

// Validaciones de campos obligatorios (RNF-04 / Flujo 4.1)
if ($id <= 0) {
    echo json_encode(['exito' => false, 'error' => 'Identificador de producto no válido.']);
    exit();
}
if (empty($nombre) || mb_strlen($nombre) < 3) {
    echo json_encode(['exito' => false, 'error' => 'El nombre del producto es obligatorio y debe tener al menos 3 caracteres.']);
    exit();
}
if (empty($categoria)) {
    echo json_encode(['exito' => false, 'error' => 'Debe seleccionar una categoría obligatoria para el producto.']);
    exit();
}
if (empty($unidad_medida)) {
    echo json_encode(['exito' => false, 'error' => 'Debe seleccionar una unidad de medida para el producto.']);
    exit();
}

// Validaciones de valores numéricos no negativos (Flujo 4.2)
if ($precio <= 0 || $stock < 0 || $stock_minimo < 0) {
    echo json_encode(['exito' => false, 'error' => 'El valor ingresado no es válido para este campo.']);
    exit();
}

// Obtener datos actuales para auditoría detallada y verificación de cambios (Flujo 4.3)
try {
    $prodActual = Producto::obtenerPorId($conexion, $id);

    if (!$prodActual) {
        echo json_encode([
            'exito' => false,
            'error' => 'El producto a modificar no existe en el sistema.'
        ]);
        exit();
    }
} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'exito' => false,
        'error' => 'No se pudo completar la consulta del producto. Intente nuevamente.'
    ]);
    exit();
}

// Detección de cambios (Flujo 4.3)
if (
    $nombre === $prodActual['nombre'] &&
    $categoria === $prodActual['categoria'] &&
    $unidad_medida === ($prodActual['unidad_medida'] ?? 'unidad') &&
    abs($precio - floatval($prodActual['precio'])) < 0.001 &&
    $stock === intval($prodActual['stock']) &&
    $stock_minimo === intval($prodActual['stock_minimo']) &&
    $descripcion === ($prodActual['descripcion'] ?? '')
) {
    echo json_encode(['exito' => false, 'error' => 'No se detectaron cambios en el formulario.']);
    exit();
}

// Ejecución de la modificación en base de datos (RNF-12)
$exito = Producto::modificarProducto(
    $conexion,
    $id,
    $nombre,
    $categoria,
    $unidad_medida,
    $precio,
    $stock,
    $stock_minimo,
    $descripcion
);

if ($exito) {
    // Registro de Auditoría RNF-18 usando TipoOperacion::MODIFICAR
    $detalleAudit = "Modificación de producto {$prodActual['codigo']} (ID: $id) - Nombre: {$nombreEscaped}, Categoría: {$categoriaEscaped}, Precio: S/ {$precio}, Stock: {$stock}, Stock Mín: {$stock_minimo}";
    Auditoria::registrar(
        $conexion,
        $usuario,
        TipoOperacion::MODIFICAR,
        'Producto',
        $id,
        $detalleAudit
    );

    echo json_encode([
        'exito' => true,
        'mensaje' => 'El producto ha sido actualizado correctamente.'
    ]);
} else {
    echo json_encode([
        'exito' => false,
        'error' => 'No se pudo completar la actualización en la base de datos.'
    ]);
}
exit();
?>