<?php
require_once __DIR__ . '/DetalleInforme.php';
require_once __DIR__ . '/GuiaProductos.php';

class InformeRecepcion {
    private int $idInforme;
    private string $fechaGeneracion;
    private string $observaciones;
    private string $estado;
    private array $fotosAdjuntas;
    private array $detalles = [];
    private GuiaProductos $guia;

    public function __construct(int $idInforme, string $fechaGeneracion, string $observaciones, string $estado, array $fotosAdjuntas, GuiaProductos $guia) {
        $this->idInforme = $idInforme;
        $this->fechaGeneracion = $fechaGeneracion;
        $this->observaciones = $observaciones;
        $this->estado = $estado;
        $this->fotosAdjuntas = $fotosAdjuntas;
        $this->guia = $guia;
    }

    public static function listar(mysqli $conexion): array {
        $sql = "SELECT i.*, g.codigo_guia FROM informe_recepcion i 
                JOIN guia_productos g ON i.id_guia = g.id_guia 
                ORDER BY i.fecha_recepcion DESC";
        $res = $conexion->query($sql);
        $informes = [];
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $informes[] = $fila;
            }
        }
        return $informes;
    }

    public static function enviar(mysqli $conexion, int $id, string $estado): bool {
        $estadoEsc = $conexion->real_escape_string($estado);
        $sql = "UPDATE informe_recepcion 
                SET estado = '$estadoEsc' 
                WHERE id_informe = $id";
        return $conexion->query($sql) === TRUE;
    }

    public function generar(): void {
        // Lógica
    }

    public function adjuntarFotos(array $fotos): void {
        $this->fotosAdjuntas = array_merge($this->fotosAdjuntas, $fotos);
    }

    public function marcarDiscrepancias(): void {
        // Lógica
    }

    public function agregarDetalle(DetalleInforme $detalle): void {
        $this->detalles[] = $detalle;
    }
}
?>
