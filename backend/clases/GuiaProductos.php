<?php
class GuiaProductos {
    private int $idGuia;
    private string $codigoGuia;
    private string $fechaEntrega;
    private string $archivoAdjunto;
    private string $estado;

    public function __construct(int $idGuia, string $codigoGuia, string $fechaEntrega, string $archivoAdjunto, string $estado) {
        $this->idGuia = $idGuia;
        $this->codigoGuia = $codigoGuia;
        $this->fechaEntrega = $fechaEntrega;
        $this->archivoAdjunto = $archivoAdjunto;
        $this->estado = $estado;
    }

    public function registrar(): void {
        // Lógica
    }

    public function validarCodigo(): bool {
        // Lógica
        return true;
    }

    public function generarComparativa(): void {
        // Lógica
    }
}
?>
