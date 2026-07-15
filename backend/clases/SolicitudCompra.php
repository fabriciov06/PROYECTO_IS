<?php
require_once __DIR__ . '/../enums/EstadoSolicitud.php';
require_once __DIR__ . '/DetalleSolicitud.php';

class SolicitudCompra {
    private int $idSolicitud;
    private string $fechaGeneracion;
    private string $estado;
    private string $observaciones;
    private string $sede;
    private string $usuarioGenerador;
    private array $detalles = [];

    public function __construct(int $idSolicitud, string $fechaGeneracion, string $estado, string $observaciones, string $sede, string $usuarioGenerador) {
        $this->idSolicitud = $idSolicitud;
        $this->fechaGeneracion = $fechaGeneracion;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
        $this->sede = $sede;
        $this->usuarioGenerador = $usuarioGenerador;
    }

    public static function consultarHistorial(mysqli $conexion): array {
        $sql = "SELECT * FROM solicitudes_compra ORDER BY fecha_solicitud DESC";
        $res = $conexion->query($sql);
        $solicitudes = [];
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $solicitudes[] = $fila;
            }
        }
        return $solicitudes;
    }

    public static function contarPendientes(mysqli $conexion): int {
        $res = $conexion->query("SELECT COUNT(*) as total FROM solicitudes_compra WHERE estado = 'Pendiente'");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    public function generar(): void {
        // Lógica
    }

    public function enviarAlAdministrador(): void {
        // Lógica
    }

    public function agregarDetalle(DetalleSolicitud $detalle): void {
        $this->detalles[] = $detalle;
    }
}
?>
