<?php
class Alerta {
    private int $idAlerta;
    private string $tipo;
    private string $mensaje;
    private string $fechaGeneracion;
    private bool $leida;
    private string $destinatario;

    public function __construct(int $idAlerta, string $tipo, string $mensaje, string $fechaGeneracion, bool $leida, string $destinatario) {
        $this->idAlerta = $idAlerta;
        $this->tipo = $tipo;
        $this->mensaje = $mensaje;
        $this->fechaGeneracion = $fechaGeneracion;
        $this->leida = $leida;
        $this->destinatario = $destinatario;
    }

    public function generar(): void {
        // Lógica
    }

    public function enviar(): void {
        // Lógica
    }

    public function marcarLeida(): void {
        $this->leida = true;
    }
}
?>
