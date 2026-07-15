<?php
require_once __DIR__ . '/../enums/EstadoFactura.php';

class Factura {
    private int $idFactura;
    private int $numeroFactura;
    private string $fechaEmision;
    private string $fechaVencimiento;
    private float $montoTotal;
    private string $estado;
    private string $archivoAdjunto;

    public function __construct(int $idFactura, int $numeroFactura, string $fechaEmision, string $fechaVencimiento, float $montoTotal, string $estado, string $archivoAdjunto) {
        $this->idFactura = $idFactura;
        $this->numeroFactura = $numeroFactura;
        $this->fechaEmision = $fechaEmision;
        $this->fechaVencimiento = $fechaVencimiento;
        $this->montoTotal = $montoTotal;
        $this->estado = $estado;
        $this->archivoAdjunto = $archivoAdjunto;
    }

    public function registrar(): void {
        // Lógica
    }

    public function calcularMonto(): float {
        // Lógica
        return $this->montoTotal;
    }

    public function consultarEstado(): string {
        return $this->estado;
    }
}
?>
