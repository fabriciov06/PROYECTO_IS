<?php
require_once __DIR__ . '/Lote.php';
require_once __DIR__ . '/MovimientoInventario.php';

class Inventario {
    private int $idInventario;
    private string $sede;
    private string $fechaActualizacion;
    private string $usuarioResponsable;
    private string $motivo;
    private array $lotes = [];
    private array $movimientos = [];

    public function __construct(int $idInventario, string $sede, string $fechaActualizacion, string $usuarioResponsable, string $motivo) {
        $this->idInventario = $idInventario;
        $this->sede = $sede;
        $this->fechaActualizacion = $fechaActualizacion;
        $this->usuarioResponsable = $usuarioResponsable;
        $this->motivo = $motivo;
    }

    public static function consultarStock(mysqli $conexion): array {
        return Producto::buscar($conexion);
    }

    public function actualizarStock(): void {
        // Lógica
    }

    public function registrarMovimiento(MovimientoInventario $movimiento): void {
        $this->movimientos[] = $movimiento;
    }
}
?>
