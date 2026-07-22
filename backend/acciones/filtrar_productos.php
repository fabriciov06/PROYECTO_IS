<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

$q = $_GET['q'] ?? '';
$f = $_GET['f'] ?? 'Todos';
$min = $_GET['min'] ?? '';
$max = $_GET['max'] ?? '';
$cat = $_GET['cat'] ?? '';

$productos = Producto::buscar($conexion, $q, $f, $min, $max, $cat);

if (!empty($productos)) {
    foreach ($productos as $fila) {
        $estado = ($fila['stock'] <= 0) ? "danger" : (($fila['stock'] < $fila['stock_minimo']) ? "warning" : "normal");
        $textoEstado = ($fila['stock'] <= 0) ? "Agotado" : (($fila['stock'] < $fila['stock_minimo']) ? "Stock Bajo" : "Normal");
        $unidad = !empty($fila['unidad_medida']) ? $fila['unidad_medida'] : 'unidad';

        $nombreEsc = htmlspecialchars($fila['nombre'], ENT_QUOTES);
        $catEsc = htmlspecialchars($fila['categoria'], ENT_QUOTES);
        
        echo "<tr>
                <td><strong>{$fila['codigo']}</strong></td>
                <td>{$fila['nombre']}</td>
                <td><span style='background: #F3F4F6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #4B5563;'>{$fila['categoria']}</span></td>
                <td style='font-weight: 600; color: #111827;'>S/ {$fila['precio']}</td>
                <td>{$fila['stock']} {$unidad}</td>
                <td style='color: #6B7280;'>{$fila['stock_minimo']} {$unidad}</td>
                <td><span class='$estado'>$textoEstado</span></td>
                <td>
                    <a href='javascript:void(0)' data-id='{$fila['id_producto']}' data-codigo='{$fila['codigo']}' data-nombre='{$nombreEsc}' data-categoria='{$catEsc}' data-precio='{$fila['precio']}' data-stock='{$fila['stock']}' data-stock-minimo='{$fila['stock_minimo']}' data-unidad-medida='{$unidad}' onclick='abrirModalEditar(this)' class='action-icon action-edit' title='Modificar producto'><i class='fa-solid fa-pen-to-square'></i></a>
                    <a href='javascript:void(0)' onclick='confirmarDesactivar({$fila['id_producto']}, \"{$nombreEsc}\")' class='action-icon action-delete' title='Desactivar producto'><i class='fa-solid fa-trash-can'></i></a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #6B7280;'>No se encontraron productos en el inventario.</td></tr>";
}
?>