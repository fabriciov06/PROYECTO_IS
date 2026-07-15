<?php
require_once __DIR__ . '/Usuario.php';

class Supervisor extends Usuario {
    private string $sede;

    public function __construct(int $idUsuario, string $nombre, string $apellido, string $email, string $contrasena, string $telefono, bool $estado, string $sede) {
        parent::__construct($idUsuario, $nombre, $apellido, $email, $contrasena, $telefono, RolUsuario::SUPERVISOR, $estado);
        $this->sede = $sede;
    }

    public function consultarStock(): void {
        // Lógica
    }

    public function generarSolicitudCompra(): void {
        // Lógica
    }

    public function consultarHistorialSolicitudes(): void {
        // Lógica
    }

    public function recibirAlerta(): void {
        // Lógica
    }
}
?>
