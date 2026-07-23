<?php
session_start();
require_once 'conexion.php';
require_once '../clases/autoload.php';

// 1. VALIDAR SESIÓN (el endpoint responde filas HTML, no JSON)
if (empty($_SESSION['usuario_logeado'])) {
    http_response_code(401);
    echo "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #9B1C1C;'>Debe iniciar sesión para consultar el catálogo.</td></tr>";
    exit();
}

// 2. VALIDAR PERMISOS DE ADMINISTRADOR
if (empty($_SESSION['rol']) || strtolower(trim((string)$_SESSION['rol'])) !== RolUsuario::ADMINISTRADOR) {
    http_response_code(403);
    echo "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #9B1C1C;'>No tiene permisos suficientes para consultar el catálogo.</td></tr>";
    exit();
}

// 3. SANITIZAR ENTRADAS (nunca confiar en el frontend)
$q = $_GET['q'] ?? '';
$f = trim($_GET['f'] ?? 'Todos');
$min = trim($_GET['min'] ?? '');
$max = trim($_GET['max'] ?? '');
$cat = trim($_GET['cat'] ?? '');

// Estado de stock: solo valores conocidos; cualquier otro se trata como 'Todos'
$estadosValidos = ['Todos', 'Normal', 'Stock Bajo', 'Agotado', 'Agotados'];
if (!in_array($f, $estadosValidos, true)) {
    $f = 'Todos';
}

// Precios: descartar valores no numéricos o negativos
if ($min !== '' && (!is_numeric($min) || floatval($min) < 0)) {
    $min = '';
}
if ($max !== '' && (!is_numeric($max) || floatval($max) < 0)) {
    $max = '';
}
// Coherencia mín/máx: si mín > máx, descartar el par (el frontend ya lo bloquea antes)
if ($min !== '' && $max !== '' && floatval($min) > floatval($max)) {
    $min = '';
    $max = '';
}

$productos = Producto::buscar($conexion, $q, $f, $min, $max, $cat);

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
        
        echo "<tr>
                <td><strong>{$codigoEsc}</strong></td>
                <td>{$nombreEsc}</td>
                <td><span style='background: #F3F4F6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #4B5563;'>{$catEsc}</span></td>
                <td style='font-weight: 600; color: #111827;'>S/ {$fila['precio']}</td>
                <td>{$fila['stock']} {$unidad}</td>
                <td style='color: #6B7280;'>{$fila['stock_minimo']} {$unidad}</td>
                <td><span class='$estado'>$textoEstado</span></td>
                <td>
                    <a href='javascript:void(0)' data-id='{$fila['id_producto']}' data-codigo='{$codigoEsc}' data-nombre='{$nombreEsc}' data-categoria='{$catEsc}' data-precio='{$fila['precio']}' data-stock='{$fila['stock']}' data-stock-minimo='{$fila['stock_minimo']}' data-unidad-medida='{$unidad}' onclick='abrirModalEditar(this)' class='action-icon action-edit' title='Modificar producto'><i class='fa-solid fa-pen-to-square'></i></a>
                    <a href='javascript:void(0)' data-id='{$idProducto}' data-codigo='{$codigoEsc}' data-nombre='{$nombreEsc}' data-categoria='{$catEsc}' data-estado='{$estadoProductoEsc}' data-stock='{$stockProducto}' onclick='abrirModalDesactivar(this)' class='action-icon action-delete' title='Desactivar producto'><i class='fa-solid fa-trash-can'></i></a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #6B7280;'>No se encontraron productos en el inventario.</td></tr>";
}
?>