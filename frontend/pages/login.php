<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MASS Inventario</title>
    <!-- Importamos la misma tipografía e iconos del dashboard -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET & TIPOGRAFÍA */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0F1B2D; /* Fondo Azul Marino Profundo */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        /* TARJETA DE LOGIN */
        .login-box {
            background: white;
            width: 100%;
            max-width: 400px;
            padding: 45px 40px;
            border-radius: 16px; /* Bordes redondeados modernos */
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4); /* Sombra elegante */
            text-align: center;
            box-sizing: border-box;
        }

        /* LOGO Y TEXTOS */
        .login-logo h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            color: #0F1B2D;
            letter-spacing: -0.5px;
        }
        .login-logo b {
            color: #FFD100; /* Amarillo MASS */
        }
        .login-box-msg {
            color: #6B7280;
            font-size: 15px;
            font-weight: 500;
            margin-top: 8px;
            margin-bottom: 35px;
        }

        /* CAJAS DE TEXTO (INPUTS) */
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 16px;
        }
        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 45px; /* Espacio para el icono */
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            color: #111827;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s ease;
            background: #F9FAFB;
        }
        .input-group input:focus {
            border-color: #0F1B2D;
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 27, 45, 0.1);
        }
        .input-group input::placeholder {
            color: #9CA3AF;
        }

        /* BOTÓN DE INGRESO */
        .btn-login {
            width: 100%;
            background: #FFD100; /* Amarillo MASS */
            color: #0F1B2D;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .btn-login:hover {
            background: #E6BC00; /* Amarillo un poco más oscuro */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 209, 0, 0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        /* MENSAJE DE ERROR (Por si las credenciales fallan) */
        .error-msg {
            background: #FEE2E2;
            color: #991B1B;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            border-left: 4px solid #EF4444;
            display: none; /* Se activa con PHP */
        }
        <?php if(isset($_GET['error'])): ?>
        .error-msg { display: block; }
        <?php endif; ?>
    </style>
</head>
<body>

    <div class="login-box">
        <div class="login-logo">
            <h2><b>MASS</b> Inventario</h2>
        </div>
        
        <div class="login-card-body">
            <p class="login-box-msg">Inicia sesión para empezar</p>
            
            <!-- Bloque de error dinámico -->
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 5px;"></i> Usuario o contraseña incorrectos.
            </div>
            
            <form action="../../backend/validar_login.php" method="POST">
                
                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="usuario" placeholder="Usuario" required autocomplete="off">
                </div>
                
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="contrasenia" placeholder="Contraseña" required>
                </div>
                
                <button type="submit" class="btn-login">Ingresar</button>
                
            </form>
        </div>
    </div>

</body>
</html>