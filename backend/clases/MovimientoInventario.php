<?php
require_once __DIR__ . '/../enums/TipoMovimiento.php';

class MovimientoInventario {
    private int $idMovimiento;
    private string $tipo;
    private int $cantidad;
    private string $fecha;
    private string $usuario;
    private string $motivo;
    private string $sede;

    public function __construct(int $idMovimiento, string $tipo, int $cantidad, string $fecha, string $usuario, string $motivo, string $sede) {
        $this->idMovimiento = $idMovimiento;
        $this->tipo = $tipo;
        $this->cantidad = $cantidad;
        $this->fecha = $fecha;
        $this->usuario = $usuario;
        $this->motivo = $motivo;
        $this->sede = $sede;
    }

    public static function consultarPorFecha(mysqli $conexion, string $inicio = '', string $fin = '', string $tipo = '', string $q = ''): array {
        $sql = "SELECT m.*, p.codigo, p.nombre 
                FROM movimientos_stock m 
                JOIN productos p ON m.id_producto = p.id_producto 
                WHERE 1=1";

        if ($inicio != '') {
            $inicioEsc = $conexion->real_escape_string($inicio);
            $sql .= " AND DATE(m.fecha_hora) >= '$inicioEsc'";
        }
        if ($fin != '') {
            $finEsc = $conexion->real_escape_string($fin);
            $sql .= " AND DATE(m.fecha_hora) <= '$finEsc'";
        }
        if ($tipo != '') {
            $tipoEsc = $conexion->real_escape_string($tipo);
            $sql .= " AND m.tipo_movimiento = '$tipoEsc'";
        }
        if ($q != '') {
            $qEsc = $conexion->real_escape_string($q);
            $sql .= " AND (p.nombre LIKE '%$qEsc%' OR p.codigo LIKE '%$qEsc%')";
        }

        $sql .= " ORDER BY m.fecha_hora DESC";
        $resultado = $conexion->query($sql);

        $movimientos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $movimientos[] = $fila;
            }
        }
        return $movimientos;
    }

    public static function obtenerUltimos(mysqli $conexion, int $limite = 5): array {
        $sql = "SELECT m.tipo_movimiento, m.cantidad, m.fecha_hora, p.nombre 
                FROM movimientos_stock m 
                JOIN productos p ON m.id_producto = p.id_producto 
                ORDER BY m.fecha_hora DESC LIMIT $limite";
        $res = $conexion->query($sql);
        $movimientos = [];
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $movimientos[] = $fila;
            }
        }
        return $movimientos;
    }

    public function registrar(): void {
        // Lógica
    }
}
?>
