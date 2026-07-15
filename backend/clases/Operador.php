<?php
require_once __DIR__ . '/Usuario.php';

class Operador extends Usuario {
    private string $turno;
    private string $sede;

    public function __construct(int $idUsuario, string $nombre, string $apellido, string $email, string $contrasena, string $telefono, bool $estado, string $turno, string $sede) {
        parent::__construct($idUsuario, $nombre, $apellido, $email, $contrasena, $telefono, RolUsuario::OPERADOR, $estado);
        $this->turno = $turno;
        $this->sede = $sede;
    }

    public static function enviarInforme(mysqli $conexion, int $id, string $estado): bool {
        return InformeRecepcion::enviar($conexion, $id, $estado);
    }
}
?>
