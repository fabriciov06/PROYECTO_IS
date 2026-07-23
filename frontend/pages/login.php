<?php
session_start();
if (isset($_SESSION['usuario_logeado'])) {
    header("Location: productos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MASS Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* RESET & BASE */
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0F1B2D;
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
            max-width: 420px;
            padding: 45px 40px;
            border-radius: 16px;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        /* LOGO Y MENSAGE */
        .login-logo h2 {
            margin: 0;
            font-size: 34px;
            font-weight: 900;
            color: #0F1B2D;
            letter-spacing: -0.5px;
        }
        .login-logo b {
            color: #FFD100;
        }
        .login-box-msg {
            color: #6B7280;
            font-size: 14px;
            font-weight: 500;
            margin-top: 8px;
            margin-bottom: 30px;
        }

        /* CAJAS DE TEXTO (INPUTS) */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group {
            position: relative;
        }
        .input-group i.input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 16px;
            pointer-events: none;
        }
        .input-group input {
            width: 100%;
            padding: 14px 45px 14px 45px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            color: #111827;
            outline: none;
            transition: all 0.3s ease;
            background: #F9FAFB;
        }
        .input-group input#usuario {
            padding-right: 15px;
        }
        .input-group input:focus {
            border-color: #0F1B2D;
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 27, 45, 0.1);
        }
        .input-group input.input-error {
            border-color: #EF4444 !important;
            background: #FEF2F2 !important;
        }
        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
            font-size: 16px;
            transition: color 0.2s;
            z-index: 5;
        }
        .toggle-password:hover {
            color: #0F1B2D;
        }
        
        .field-error-text {
            color: #DC2626;
            font-size: 12px;
            font-weight: 600;
            margin-top: 5px;
            display: none;
        }

        /* BOTÓN DE INGRESO */
        .btn-login {
            width: 100%;
            background: #FFD100;
            color: #0F1B2D;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-login:hover {
            background: #E6BC00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 209, 0, 0.3);
        }
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* MENSAJE DE ERROR GENERAL */
        .error-msg {
            background: #FEE2E2;
            color: #991B1B;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            border-left: 4px solid #EF4444;
            display: none;
            text-align: left;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <div class="login-logo">
            <h2><b>MASS</b> Inventario</h2>
        </div>
        
        <div class="login-card-body">
            <p class="login-box-msg">Inicia sesión con tus credenciales</p>
            
            <!-- Bloque de alerta de error -->
            <div id="alertError" class="error-msg">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 5px;"></i>
                <span id="alertErrorText">Usuario o contraseña incorrectos.</span>
            </div>
            
            <form id="formLogin" novalidate autocomplete="off">
                <!-- CAMPO USUARIO -->
                <div class="form-group">
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" id="usuario" name="usuario" placeholder="Usuario" tabindex="1" autocomplete="off">
                    </div>
                    <div id="errorUsuario" class="field-error-text">Este campo es obligatorio.</div>
                </div>
                
                <!-- CAMPO CONTRASEÑA CON ÍCONO DE OJO -->
                <div class="form-group">
                    <div class="input-group">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="contrasenia" name="contrasenia" placeholder="Contraseña" tabindex="2">
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword" title="Mostrar/Ocultar contraseña"></i>
                    </div>
                    <div id="errorContrasenia" class="field-error-text">Este campo es obligatorio.</div>
                </div>
                
                <!-- BOTÓN INGRESAR -->
                <button type="submit" id="btnIngresar" class="btn-login" tabindex="3">
                    <span>Ingresar</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        const formLogin = document.getElementById("formLogin");
        const inputUsuario = document.getElementById("usuario");
        const inputContrasenia = document.getElementById("contrasenia");
        const togglePassword = document.getElementById("togglePassword");
        const btnIngresar = document.getElementById("btnIngresar");
        const alertError = document.getElementById("alertError");
        const alertErrorText = document.getElementById("alertErrorText");

        const errorUsuario = document.getElementById("errorUsuario");
        const errorContrasenia = document.getElementById("errorContrasenia");

        // Paso 3: Mostrar / Ocultar Contraseña
        togglePassword.addEventListener("click", function() {
            const isPassword = inputContrasenia.type === "password";
            inputContrasenia.type = isPassword ? "text" : "password";
            this.classList.toggle("fa-eye", !isPassword);
            this.classList.toggle("fa-eye-slash", isPassword);
        });

        // Limpiar errores visuales
        function limpiarErrores() {
            alertError.style.display = "none";
            inputUsuario.classList.remove("input-error");
            inputContrasenia.classList.remove("input-error");
            errorUsuario.style.display = "none";
            errorContrasenia.style.display = "none";
        }

        // Paso 5-6: Enviar formulario
        formLogin.addEventListener("submit", function(e) {
            e.preventDefault();
            limpiarErrores();

            const userVal = inputUsuario.value.trim();
            const passVal = inputContrasenia.value.trim();
            let hayError = false;

            // Flujo Alterno 6.1: Campos Vacíos
            if (!userVal) {
                inputUsuario.classList.add("input-error");
                errorUsuario.style.display = "block";
                hayError = true;
            }

            if (!passVal) {
                inputContrasenia.classList.add("input-error");
                errorContrasenia.style.display = "block";
                hayError = true;
            }

            if (hayError) {
                if (!userVal) inputUsuario.focus();
                else if (!passVal) inputContrasenia.focus();
                return;
            }

            // Paso 6: RNF-05 Indicador de Carga
            btnIngresar.disabled = true;
            btnIngresar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Procesando...</span>';

            const formData = new FormData();
            formData.append("usuario", userVal);
            formData.append("contrasenia", passVal);

            // Pasos 7-8: Validación asíncrona contra backend
            fetch("../../backend/acciones/validar_login.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    // Paso 8: Redirección exitosa
                    window.location.href = data.redirect || "productos.php";
                } else {
                    // Restaurar botón
                    btnIngresar.disabled = false;
                    btnIngresar.innerHTML = '<span>Ingresar</span>';

                    // Flujo 6.1 backend fallback
                    if (data.campo_vacio === 'usuario') {
                        inputUsuario.classList.add("input-error");
                        errorUsuario.style.display = "block";
                        inputUsuario.focus();
                    } else if (data.campo_vacio === 'contrasenia') {
                        inputContrasenia.classList.add("input-error");
                        errorContrasenia.style.display = "block";
                        inputContrasenia.focus();
                    }

                    // Flujos 7.1, 7.2, 7.3: Mostrar mensaje de error correspondiente
                    alertErrorText.innerText = data.error || "Usuario o contraseña incorrectos.";
                    alertError.style.display = "block";
                }
            })
            .catch(() => {
                btnIngresar.disabled = false;
                btnIngresar.innerHTML = '<span>Ingresar</span>';
                alertErrorText.innerText = "Error al conectar con el servidor. Intente nuevamente.";
                alertError.style.display = "block";
            });
        });
    </script>
</body>
</html>