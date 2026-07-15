<?php
require_once __DIR__ . '/Producto.php';

class Lote {
    private int $idLote;
    private string $codigoLote;
    private string $fechaIngreso;
    private string $fechaSalidaEstimada;
    private int $cantidad;
    private string $estado;
    private Producto $producto;

    public function __construct(int $idLote, string $codigoLote, string $fechaIngreso, string $fechaSalidaEstimada, int $cantidad, string $estado, Producto $producto) {
        $this->idLote = $idLote;
        $this->codigoLote = $codigoLote;
        $this->fechaIngreso = $fechaIngreso;
        $this->fechaSalidaEstimada = $fechaSalidaEstimada;
        $this->cantidad = $cantidad;
        $this->estado = $estado;
        $this->producto = $producto;
    }

    public function registrar(): void {
        // Lógica
    }

    public function actualizarEstado(string $nuevoEstado): void {
        $this->estado = $nuevoEstado;
    }
}
?>
