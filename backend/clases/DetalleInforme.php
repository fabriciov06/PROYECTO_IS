<?php
require_once __DIR__ . '/../enums/EstadoProductoRecibido.php';

class DetalleInforme {
    private int $idDetalle;
    private string $productoEsperado;
    private int $cantidadEsperada;
    private int $cantidadRecibida;
    private string $observacion;
    private string $estadoProducto;

    public function __construct(int $idDetalle, string $productoEsperado, int $cantidadEsperada, int $cantidadRecibida, string $observacion, string $estadoProducto) {
        $this->idDetalle = $idDetalle;
        $this->productoEsperado = $productoEsperado;
        $this->cantidadEsperada = $cantidadEsperada;
        $this->cantidadRecibida = $cantidadRecibida;
        $this->observacion = $observacion;
        $this->estadoProducto = $estadoProducto;
    }

    public function registrar(): void {
        // Lógica
    }

    public function detectarDiscrepancia(): bool {
        return $this->cantidadEsperada !== $this->cantidadRecibida;
    }
}
?>
