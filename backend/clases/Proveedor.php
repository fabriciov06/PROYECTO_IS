<?php
require_once __DIR__ . '/Usuario.php';

class Proveedor extends Usuario {
    private string $RUC;
    private string $razonSocial;
    private string $direccion;
    private string $contacto;

    public function __construct(int $idUsuario, string $nombre, string $apellido, string $email, string $contrasena, string $telefono, bool $estado, string $RUC, string $razonSocial, string $direccion, string $contacto) {
        parent::__construct($idUsuario, $nombre, $apellido, $email, $contrasena, $telefono, RolUsuario::PROVEEDOR, $estado);
        $this->RUC = $RUC;
        $this->razonSocial = $razonSocial;
        $this->direccion = $direccion;
        $this->contacto = $contacto;
    }

    public static function cargarSolucionReclamo(mysqli $conexion, int $id, string $solucion, string $estado): bool {
        return Reclamo::cargarSolucion($conexion, $id, $solucion, $estado);
    }
}
?>
