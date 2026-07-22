<?php
require_once __DIR__ . '/../enums/TipoOperacion.php';

class Auditoria {
    private int $idAuditoria;
    private string $usuario;
    private string $fecha;
    private string $tipo;
    private string $entidad;
    private int $idRegistro;
    private string $detalle;

    public function __construct(int $idAuditoria, string $usuario, string $fecha, string $tipo, string $entidad, int $idRegistro, string $detalle) {
        $this->idAuditoria = $idAuditoria;
        $this->usuario = $usuario;
        $this->fecha = $fecha;
        $this->tipo = $tipo;
        $this->entidad = $entidad;
        $this->idRegistro = $idRegistro;
        $this->detalle = $detalle;
    }

    public static function registrar(mysqli $conexion, string $usuario, string $tipo, string $entidad, int $idRegistro, string $detalle): bool {
        $usuarioEsc = $conexion->real_escape_string($usuario);
        $tipoEsc = $conexion->real_escape_string($tipo);
        $entidadEsc = $conexion->real_escape_string($entidad);
        $detalleEsc = $conexion->real_escape_string($detalle);
        $fechaActual = date('Y-m-d H:i:s');

        $sql = "INSERT INTO auditoria (usuario, fecha, tipo, entidad, idRegistro, detalle) 
                VALUES ('$usuarioEsc', '$fechaActual', '$tipoEsc', '$entidadEsc', $idRegistro, '$detalleEsc')";
        return $conexion->query($sql) === TRUE;
    }
}
?>
