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
    protected string $estado;

    public function __construct(int $idUsuario, string $nombre, string $apellido, string $email, string $contrasena, string $telefono, string $rol, string $estado) {
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->telefono = $telefono;
        $this->rol = $rol;
        $this->estado = $estado;
    }

    public static function login(mysqli $conexion, string $user, string $pass): array {
        $userClean = trim($user);
        $passClean = trim($pass);

        // Flujo 6.1: Validación de campos obligatorios en el backend
        if (empty($userClean) || empty($passClean)) {
            return [
                'exito' => false,
                'error' => 'Este campo es obligatorio.',
                'campo_vacio' => empty($userClean) ? 'usuario' : 'contrasenia'
            ];
        }

        // Sanitización (RNF-17)
        $userEsc = $conexion->real_escape_string($userClean);
        $res = $conexion->query("SELECT * FROM usuarios WHERE usuario = '$userEsc'");

        if (!$res || $res->num_rows === 0) {
            // RNF-14: Credenciales incorrectas sin indicar cuál de los dos falló por seguridad
            return [
                'exito' => false,
                'error' => 'Usuario o contraseña incorrectos.'
            ];
        }

        $fila = $res->fetch_assoc();
        $idUsuario = (int)$fila['id_usuario'];
        $estado = trim($fila['estado'] ?? 'Activo');
        $intentos = (int)($fila['intentos_fallidos'] ?? 0);

        // Flujo 7.2: Cuenta desactivada
        if (strtolower($estado) === 'desactivado') {
            return [
                'exito' => false,
                'error' => 'Su cuenta ha sido desactivada. Contacte al administrador.'
            ];
        }

        // Flujo 7.3 (RNF-20): Límite de intentos fallidos / Cuenta bloqueada
        if (strtolower($estado) === 'bloqueado' || $intentos >= 5) {
            if (strtolower($estado) !== 'bloqueado') {
                $conexion->query("UPDATE usuarios SET estado = 'Bloqueado' WHERE id_usuario = $idUsuario");
            }
            return [
                'exito' => false,
                'error' => 'Cuenta bloqueada temporalmente por múltiples intentos fallidos. Contacte al administrador.'
            ];
        }

        // Verificar contraseña (soporta texto plano o password_verify)
        $passwordValida = ($passClean === $fila['contrasenia'] || password_verify($passClean, $fila['contrasenia']));

        if (!$passwordValida) {
            $nuevosIntentos = $intentos + 1;
            if ($nuevosIntentos >= 5) {
                $conexion->query("UPDATE usuarios SET intentos_fallidos = $nuevosIntentos, estado = 'Bloqueado' WHERE id_usuario = $idUsuario");
                return [
                    'exito' => false,
                    'error' => 'Cuenta bloqueada temporalmente por múltiples intentos fallidos. Contacte al administrador.'
                ];
            } else {
                $conexion->query("UPDATE usuarios SET intentos_fallidos = $nuevosIntentos WHERE id_usuario = $idUsuario");
                return [
                    'exito' => false,
                    'error' => 'Usuario o contraseña incorrectos.'
                ];
            }
        }

        // Login Exitoso (Pasos 7-8 & Flujo 8.1 - Sesión Activa)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $nuevaSessionId = session_id();

        $conexion->query("UPDATE usuarios SET intentos_fallidos = 0, session_id = '$nuevaSessionId' WHERE id_usuario = $idUsuario");

        $_SESSION['usuario_logeado'] = $fila['nombre'];
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['rol'] = strtolower($fila['rol']);
        $_SESSION['session_id'] = $nuevaSessionId;

        $redirect = '../../frontend/pages/productos.php';

        return [
            'exito' => true,
            'mensaje' => 'Autenticación exitosa.',
            'redirect' => $redirect
        ];
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }

    public function cambiarContrasena(string $nuevaContrasena): bool {
        $this->contrasena = $nuevaContrasena;
        return true;
    }
}
?>
