<?php
require_once __DIR__ . '/Usuario.php';

class Administrador extends Usuario {
    private int $nivelAcceso;

    public function __construct(int $idUsuario, string $nombre, string $apellido, string $email, string $contrasena, string $telefono, bool $estado, int $nivelAcceso) {
        parent::__construct($idUsuario, $nombre, $apellido, $email, $contrasena, $telefono, RolUsuario::ADMINISTRADOR, $estado);
        $this->nivelAcceso = $nivelAcceso;
    }

    public static function aprobarSolicitud(mysqli $conexion, int $id): bool {
        $sql = "UPDATE solicitudes_compra 
                SET estado = 'Aprobada', 
                    fecha_evaluacion = CURRENT_TIMESTAMP 
                WHERE id_solicitud = $id";
        return $conexion->query($sql) === TRUE;
    }

    public static function rechazarSolicitud(mysqli $conexion, int $id, string $motivo = ''): bool {
        $motivoEscaped = $conexion->real_escape_string($motivo);
        $sql = "UPDATE solicitudes_compra 
                SET estado = 'Rechazada', 
                    fecha_evaluacion = CURRENT_TIMESTAMP, 
                    motivo_rechazo = '$motivoEscaped' 
                WHERE id_solicitud = $id";
        return $conexion->query($sql) === TRUE;
    }

    public static function consultarHistorialMovimientos(mysqli $conexion, string $inicio = '', string $fin = '', string $tipo = '', string $q = ''): array {
        return MovimientoInventario::consultarPorFecha($conexion, $inicio, $fin, $tipo, $q);
    }
}
?>
