<?php
require_once 'conexion.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';
$f = isset($_GET['f']) ? $_GET['f'] : 'Todos';
$min = isset($_GET['min']) ? $_GET['min'] : '';
$max = isset($_GET['max']) ? $_GET['max'] : '';
$cat = isset($_GET['cat']) ? $_GET['cat'] : '';

$sql = "SELECT * FROM productos WHERE 1=1";

if ($q != '') {
    $sql .= " AND (nombre LIKE '%$q%' OR codigo LIKE '%$q%')";
}
if ($f == 'Stock Bajo') {
    $sql .= " AND stock < stock_minimo AND stock > 0";
} elseif ($f == 'Agotados') {
    $sql .= " AND stock <= 0";
}
if ($min != '') {
    $sql .= " AND precio >= $min";
}
if ($max != '') {
    $sql .= " AND precio <= $max";
}
if ($cat != '') {
    $sql .= " AND categoria = '$cat'";
}

$sql .= " ORDER BY codigo ASC";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $estado = ($fila['stock'] <= 0) ? "danger" : (($fila['stock'] < $fila['stock_minimo']) ? "warning" : "normal");
        $textoEstado = ($fila['stock'] <= 0) ? "Agotado" : (($fila['stock'] < $fila['stock_minimo']) ? "Stock Bajo" : "Normal");
        
        echo "<tr>
                <td><strong>{$fila['codigo']}</strong></td>
                <td>{$fila['nombre']}</td>
                <td><span style='background: #F3F4F6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #4B5563;'>{$fila['categoria']}</span></td>
                <td style='font-weight: 600; color: #111827;'>S/ {$fila['precio']}</td>
                <td>{$fila['stock']}</td>
                <td style='color: #6B7280;'>{$fila['stock_minimo']}</td>
                <td><span class='$estado'>$textoEstado</span></td>
                <td>
                    <a href='#' data-id='{$fila['id_producto']}' data-nombre='{$fila['nombre']}' data-precio='{$fila['precio']}' data-stock='{$fila['stock']}' onclick='abrirModalEditar(this)' class='action-icon action-edit' title='Editar'><i class='fa-solid fa-pen-to-square'></i></a>
                    <a href='../../backend/eliminar_producto.php?id={$fila['id_producto']}' class='action-icon action-delete' title='Eliminar' onclick='return confirm(\"¿Seguro que deseas eliminar el producto {$fila['nombre']}?\")'><i class='fa-solid fa-trash-can'></i></a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center; padding: 40px; color: #6B7280;'>No se encontraron productos con los filtros aplicados.</td></tr>";
}
?>