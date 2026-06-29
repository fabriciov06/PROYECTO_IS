<?php
include("conexion.php");

$busqueda = isset($_GET['q']) ? $conexion->real_escape_string($_GET['q']) : '';
$filtro = isset($_GET['f']) ? $_GET['f'] : 'Todos';
$min = isset($_GET['min']) && $_GET['min'] !== '' ? floatval($_GET['min']) : 0;
$max = isset($_GET['max']) && $_GET['max'] !== '' ? floatval($_GET['max']) : 999999;

$sql = "SELECT * FROM productos WHERE (nombre LIKE '%$busqueda%' OR codigo LIKE '%$busqueda%') 
        AND precio BETWEEN $min AND $max";

if ($filtro == 'Stock Bajo') $sql .= " AND stock < stock_minimo AND stock > 0";
if ($filtro == 'Agotados') $sql .= " AND stock = 0";

$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $estado = ($fila['stock'] <= 0) ? "danger" : (($fila['stock'] < $fila['stock_minimo']) ? "warning" : "normal");
        $textoEstado = ($fila['stock'] <= 0) ? "Agotado" : (($fila['stock'] < $fila['stock_minimo']) ? "Stock Bajo" : "Normal");
        
        echo "<tr>
                <td>{$fila['codigo']}</td>
                <td>{$fila['nombre']}</td>
                <td>{$fila['categoria']}</td>
                <td>S/ {$fila['precio']}</td>
                <td>{$fila['stock']}</td>
                <td>{$fila['stock_minimo']}</td>
                <td><span class='$estado'>$textoEstado</span></td>
                <td>
                    <a href='#' data-id='{$fila['id_producto']}' data-nombre='{$fila['nombre']}' data-precio='{$fila['precio']}' data-stock='{$fila['stock']}' onclick='abrirModalEditar(this)' style='color: #007bff; margin-right: 15px; font-size: 16px;' title='Editar'><i class='fa-solid fa-pencil'></i></a>
                    <a href='../../backend/eliminar_producto.php?id={$fila['id_producto']}' style='color: #dc3545; font-size: 16px;' title='Eliminar' onclick='return confirm(\"¿Seguro que deseas eliminar este producto?\")'><i class='fa-solid fa-trash'></i></a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No se encontraron productos.</td></tr>";
}
?>