<?php
require_once 'conexion.php';
require_once '../clases/autoload.php';

$inicio = $_GET['inicio'] ?? '';
$fin = $_GET['fin'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$q = $_GET['q'] ?? '';

$movimientos = Administrador::consultarHistorialMovimientos($conexion, $inicio, $fin, $tipo, $q);

if (!empty($movimientos)) {
    foreach ($movimientos as $fila) {
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