<?php
require_once __DIR__ . '/../enums/RolUsuario.php';

abstract class Usuario {
    protected int $idUsuario;
    protected string $nombre;
    protected string $apellido;
    protected string $email;
    protected string $contrasena;
    protected string $telefono;
    protected string $rol;
    protected bool $estado;

    public function __construct(int $idUsuario, string $nombre, string $apellido, string $email, string $contrasena, string $telefono, string $rol, bool $estado) {
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->telefono = $telefono;
        $this->rol = $rol;
        $this->estado = $estado;
    }

    public static function login(mysqli $conexion, string $user, string $pass): bool {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contrasenia = ?");
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $_SESSION['usuario_logeado'] = $fila['nombre'];
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = strtolower($fila['rol']);
            return true;
        }
        return false;
    }

    public function logout(): void {
        session_destroy();
    }

    public function cambiarContrasena(string $nuevaContrasena): bool {
        $this->contrasena = $nuevaContrasena;
        return true;
    }
}
?>
