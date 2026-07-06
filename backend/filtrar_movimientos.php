<?php
require_once 'conexion.php';

$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
$fin = isset($_GET['fin']) ? $_GET['fin'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$q = isset($_GET['q']) ? $_GET['q'] : '';

// Consulta SQL conectando movimientos_stock con productos
$sql = "SELECT m.*, p.codigo, p.nombre 
        FROM movimientos_stock m 
        JOIN productos p ON m.id_producto = p.id_producto 
        WHERE 1=1";

if ($inicio != '') {
    $sql .= " AND DATE(m.fecha_hora) >= '$inicio'";
}
if ($fin != '') {
    $sql .= " AND DATE(m.fecha_hora) <= '$fin'";
}
if ($tipo != '') {
    $sql .= " AND m.tipo_movimiento = '$tipo'";
}
if ($q != '') {
    $sql .= " AND (p.nombre LIKE '%$q%' OR p.codigo LIKE '%$q%')";
}

$sql .= " ORDER BY m.fecha_hora DESC";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        
        $fecha = date("d/m/Y", strtotime($fila['fecha_hora']));
        $hora = date("h:i A", strtotime($fila['fecha_hora']));
        
        $badge = "";
        $clase_cantidad = "";
        $signo = "";

        if ($fila['tipo_movimiento'] == 'Entrada') {
            $badge = "<span class='badge-entrada'><i class='fa-solid fa-arrow-down'></i> Entrada</span>";
            $clase_cantidad = "cantidad-pos";
            $signo = "+";
        } elseif ($fila['tipo_movimiento'] == 'Salida') {
            $badge = "<span class='badge-salida'><i class='fa-solid fa-arrow-up'></i> Salida</span>";
            $clase_cantidad = "cantidad-neg";
            $signo = "-";
        } else {
            $badge = "<span class='badge-ajuste'><i class='fa-solid fa-rotate'></i> Ajuste</span>";
            $clase_cantidad = "cantidad-neg";
            $signo = "-";
        }

        echo "<tr>
                <td style='color: #6B7280; font-size: 13px;'>{$fecha}<br>{$hora}</td>
                <td><strong>{$fila['codigo']}</strong> - {$fila['nombre']}</td>
                <td>{$badge}</td>
                <td class='{$clase_cantidad}'>{$signo}{$fila['cantidad']} und</td>
                <td>{$fila['responsable']}</td>
                <td>{$fila['motivo']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: #6B7280;'>No se encontraron movimientos con los filtros aplicados.</td></tr>";
}
?>