<?php
class DetalleSolicitud {
    private int $idDetalle;
    private string $producto;
    private int $cantidadRequerida;
    private float $precioEstimado;

    public function __construct(int $idDetalle, string $producto, int $cantidadRequerida, float $precioEstimado) {
        $this->idDetalle = $idDetalle;
        $this->producto = $producto;
        $this->cantidadRequerida = $cantidadRequerida;
        $this->precioEstimado = $precioEstimado;
    }

    public function agregar(): void {
        // Lógica
    }

    public function modificar(): void {
        // Lógica
    }
}
?>
