<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../clases/autoload.php';

// 1. VALIDAR MÉTODO HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode([
        'exito' => false,
        'error' => 'Método de solicitud no permitido.'
    ]);
    exit();
}

// 2. VALIDAR SESIÓN (Flujo 2.1)
if (empty($_SESSION['usuario_logeado']) || empty($_SESSION['rol'])) {
    http_response_code(401);
    echo json_encode([
        'exito' => false,
        'error' => 'Su sesión ha finalizado. Vuelva a iniciar sesión.'
    ]);
    exit();
}

$usuario = (string)$_SESSION['usuario_logeado'];

// 3. VALIDAR PERMISOS DE ROL
if (strtolower(trim((string)$_SESSION['rol'])) !== RolUsuario::ADMINISTRADOR) {
    http_response_code(403);
    echo json_encode([
        'exito' => false,
        'error' => 'No tiene permisos suficientes para desactivar productos.'
    ]);
    exit();
}

// 4. VALIDAR ID DE ENTRADA
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id <= 0) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'error' => 'Debe seleccionar un producto válido.'
    ]);
    exit();
}

// BLOQUE PRINCIPAL CON MANEJO DE EXCEPCIONES
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once __DIR__ . '/conexion.php';

    // 5. OBTENER Y VALIDAR PRODUCTO
    $producto = Producto::obtenerPorId($conexion, $id);
    if ($producto === null) {
        http_response_code(404);
        echo json_encode([
            'exito' => false,
            'error' => 'El producto seleccionado no existe.'
        ]);
        exit();
    }

    // 6. VALIDAR ESTADO (FA-02)
    $estado = strtolower(trim((string)($producto['estado'] ?? '')));
    if ($estado === 'desactivado') {
        http_response_code(409);
        echo json_encode([
            'exito' => false,
            'error' => 'El producto seleccionado ya se encuentra desactivado.'
        ]);
        exit();
    }

    // 7. VALIDAR OPERACIONES PENDIENTES (FA-03)
    if (Producto::tieneOperacionesPendientes($conexion, $id)) {
        http_response_code(409);
        echo json_encode([
            'exito' => false,
            'error' => 'No es posible desactivar este producto porque posee operaciones pendientes.'
        ]);
        exit();
    }

    // 8. EJECUTAR DESACTIVACIÓN
    $exito = Catalogo::desactivarProducto($conexion, $id, $usuario);
    if ($exito !== true) {
        throw new RuntimeException("Error en la desactivación del producto.");
    }

    http_response_code(200);
    echo json_encode([
        'exito' => true,
        'mensaje' => 'El producto ha sido desactivado correctamente.'
    ]);
    exit();

} catch (Throwable $e) {
    error_log('[DesactivarProducto] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => 'No se pudo completar la desactivación. Intente nuevamente.'
    ]);
    exit();
}
?>