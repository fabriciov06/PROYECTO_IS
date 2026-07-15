<?php
require_once __DIR__ . '/../enums/EstadoReclamo.php';
require_once __DIR__ . '/InformeRecepcion.php';

class Reclamo {
    private int $idReclamo;
    private string $fechaGeneracion;
    private string $motivo;
    private string $descripcion;
    private string $estado;
    private string $fechaCierre;
    private string $solucionDescripcion;
    private string $archivoSolucion;
    private string $plazoLimite;
    private InformeRecepcion $informe;

    public function __construct(int $idReclamo, string $fechaGeneracion, string $motivo, string $descripcion, string $estado, string $fechaCierre, string $solucionDescripcion, string $archivoSolucion, string $plazoLimite, InformeRecepcion $informe) {
        $this->idReclamo = $idReclamo;
        $this->fechaGeneracion = $fechaGeneracion;
        $this->motivo = $motivo;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
        $this->fechaCierre = $fechaCierre;
        $this->solucionDescripcion = $solucionDescripcion;
        $this->archivoSolucion = $archivoSolucion;
        $this->plazoLimite = $plazoLimite;
        $this->informe = $informe;
    }

    public static function listar(mysqli $conexion): array {
        $sql = "SELECT r.*, i.codigo_informe FROM reclamos r 
                JOIN informe_recepcion i ON r.id_informe = i.id_informe 
                ORDER BY r.id_reclamo DESC";
        $res = $conexion->query($sql);
        $reclamos = [];
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $reclamos[] = $fila;
            }
        }
        return $reclamos;
    }

    public static function contarActivos(mysqli $conexion): int {
        $res = $conexion->query("SELECT COUNT(*) as total FROM reclamos WHERE estado != 'Cerrado'");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    public static function cargarSolucion(mysqli $conexion, int $id, string $solucion, string $estado): bool {
        $solucionEsc = $conexion->real_escape_string($solucion);
        $estadoEsc = $conexion->real_escape_string($estado);

        $sql = "UPDATE reclamos 
                SET solucion_proveedor = '$solucionEsc', 
                    estado = '$estadoEsc' 
                WHERE id_reclamo = $id";
        return $conexion->query($sql) === TRUE;
    }

    public function cerrar(): void {
        $this->estado = EstadoReclamo::CERRADO;
        $this->fechaCierre = date('Y-m-d H:i:s');
    }

    public function generar(): void {
        // Lógica
    }

    public function enviarAlProveedor(): void {
        // Lógica
    }

    public function verificarPlazo(): bool {
        return true;
    }
}
?>
