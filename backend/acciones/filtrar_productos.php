<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

// 1. VALIDAR SESIÓN
if (empty($_SESSION['usuario_logeado'])) {
    http_response_code(401);
    echo json_encode([
        'exito' => false,
        'error' => 'Debe iniciar sesión para consultar el catálogo.'
    ]);
    exit();
}

// 2. VALIDAR PERMISOS DE ADMINISTRADOR (RNF-14)
if (empty($_SESSION['rol']) || strtolower(trim((string)$_SESSION['rol'])) !== RolUsuario::ADMINISTRADOR) {
    http_response_code(403);
    echo json_encode([
        'exito' => false,
        'error' => 'No tiene permisos suficientes para consultar el catálogo.'
    ]);
    exit();
}

try {
    // 3. SANITIZAR ENTRADAS
    $q = $_GET['q'] ?? '';
    $f = trim($_GET['f'] ?? 'Todos');
    $min = trim($_GET['min'] ?? '');
    $max = trim($_GET['max'] ?? '');
    $cat = trim($_GET['cat'] ?? '');
    $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1;
    $limite = filter_input(INPUT_GET, 'limite', FILTER_VALIDATE_INT) ?: 25;

    $estadosValidos = ['Todos', 'Normal', 'Stock Bajo', 'Agotado', 'Agotados'];
    if (!in_array($f, $estadosValidos, true)) {
        $f = 'Todos';
    }

    if ($min !== '' && (!is_numeric($min) || floatval($min) < 0)) {
        $min = '';
    }
    if ($max !== '' && (!is_numeric($max) || floatval($max) < 0)) {
        $max = '';
    }
    if ($min !== '' && $max !== '' && floatval($min) > floatval($max)) {
        $min = '';
        $max = '';
    }

    // 4. CONSULTA CON CONTEO Y PAGINACIÓN GLOBAL (Paso 4 / RNF-11)
    $res = Producto::buscarPaginado($conexion, $q, $f, $min, $max, $cat, $pagina, $limite);

    $productos = $res['productos'];
    $total = $res['total'];
    $paginaActual = $res['pagina_actual'];
    $totalPaginas = $res['total_paginas'];
    $limiteActual = $res['limite'];
    $desde = $res['desde'];
    $hasta = $res['hasta'];

    $html = '';

    if (!empty($productos)) {
        foreach ($productos as $fila) {
            $estado = ($fila['stock'] <= 0) ? "danger" : (($fila['stock'] < $fila['stock_minimo']) ? "warning" : "normal");
            $textoEstado = ($fila['stock'] <= 0) ? "Agotado" : (($fila['stock'] < $fila['stock_minimo']) ? "Stock Bajo" : "Normal");
            $unidad = !empty($fila['unidad_medida']) ? $fila['unidad_medida'] : 'unidad';

            $idProducto = (int)$fila['id_producto'];
            $stockProducto = (int)$fila['stock'];
            $codigoEsc = htmlspecialchars((string)$fila['codigo'], ENT_QUOTES, 'UTF-8');
            $nombreEsc = htmlspecialchars((string)$fila['nombre'], ENT_QUOTES, 'UTF-8');
            $catEsc = htmlspecialchars((string)$fila['categoria'], ENT_QUOTES, 'UTF-8');
            $estadoProductoEsc = htmlspecialchars((string)($fila['estado'] ?? ''), ENT_QUOTES, 'UTF-8');
            $descEsc = htmlspecialchars((string)($fila['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
            
            $html .= "<tr>
                    <td style='white-space: nowrap;'><strong>{$codigoEsc}</strong></td>
                    <td style='min-width: 200px;'>{$nombreEsc}</td>
                    <td style='white-space: nowrap;'><span style='background: #F3F4F6; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #4B5563; display: inline-block; white-space: nowrap;'>{$catEsc}</span></td>
                    <td style='font-weight: 600; color: #111827; white-space: nowrap;'>S/ {$fila['precio']}</td>
                    <td style='white-space: nowrap;'>{$fila['stock']} {$unidad}</td>
                    <td style='color: #6B7280; white-space: nowrap;'>{$fila['stock_minimo']} {$unidad}</td>
                    <td style='white-space: nowrap;'><span class='$estado' style='display: inline-block; white-space: nowrap;'>$textoEstado</span></td>
                    <td style='white-space: nowrap;'>
                        <a href='javascript:void(0)' data-id='{$fila['id_producto']}' data-codigo='{$codigoEsc}' data-nombre='{$nombreEsc}' data-categoria='{$catEsc}' data-precio='{$fila['precio']}' data-stock='{$fila['stock']}' data-stock-minimo='{$fila['stock_minimo']}' data-unidad-medida='{$unidad}' data-descripcion='{$descEsc}' onclick='abrirModalEditar(this)' class='action-icon action-edit' title='Modificar producto'><i class='fa-solid fa-pen-to-square'></i></a>
                        <a href='javascript:void(0)' data-id='{$idProducto}' data-codigo='{$codigoEsc}' data-nombre='{$nombreEsc}' data-categoria='{$catEsc}' data-estado='{$estadoProductoEsc}' data-stock='{$stockProducto}' onclick='abrirModalDesactivar(this)' class='action-icon action-delete' title='Desactivar producto'><i class='fa-solid fa-trash-can'></i></a>
                    </td>
                  </tr>";
        }
    } else {
        // Verificar si es por búsqueda/filtro o por catálogo vacío
        $hayFiltros = (trim($q) !== '' && mb_strlen(trim($q), 'UTF-8') >= 2) || $f !== 'Todos' || $cat !== '' || $min !== '' || $max !== '';

        if ($hayFiltros) {
            // Flujo Alterno 8.1: Sin resultados de filtro
            $html = "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #6B7280; font-weight: 500;'><i class='fa-solid fa-magnifying-glass' style='font-size: 24px; color: #9CA3AF; margin-bottom: 10px; display: block;'></i>No se encontraron productos en el inventario.</td></tr>";
        } else {
            // Flujo Alterno 2.1: Sin productos registrados
            $html = "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #6B7280; font-weight: 500;'><i class='fa-solid fa-box-open' style='font-size: 24px; color: #9CA3AF; margin-bottom: 10px; display: block;'></i>No hay productos registrados. Presione <kbd style='background:#E5E7EB; padding:2px 6px; border-radius:4px; font-weight:700;'>Alt+N</kbd> para agregar el primero.</td></tr>";
        }
    }

    echo json_encode([
        'exito' => true,
        'html' => $html,
        'total' => $total,
        'pagina_actual' => $paginaActual,
        'total_paginas' => $totalPaginas,
        'limite' => $limiteActual,
        'desde' => $desde,
        'hasta' => $hasta
    ]);
    exit();

} catch (Throwable $e) {
    // Flujo Alterno 2.2: Error de carga (RNF-23)
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => 'No se pudo cargar el listado. Intente refrescando la página.',
        'html' => "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #DC2626; font-weight: 600;'><i class='fa-solid fa-circle-exclamation' style='font-size: 24px; margin-bottom: 10px; display: block;'></i>No se pudo cargar el listado. Intente refrescando la página.</td></tr>"
    ]);
    exit();
}
?>