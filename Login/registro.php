<?php
session_start();

$mensaje_titulo = "";
$mensaje_cuerpo = "";
$tipo_alerta = ""; 

if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_titulo = "¡Carga Exitosa! 🎉";
    $mensaje_cuerpo = $_SESSION['mensaje_exito'];
    $tipo_alerta = "exito";
    unset($_SESSION['mensaje_exito']); 
} elseif (isset($_SESSION['mensaje_error'])) { 
    $mensaje_titulo = "¡Error en la Operación! 🚫"; 
    $mensaje_cuerpo = $_SESSION['mensaje_error'];
    $tipo_alerta = "error"; 
    unset($_SESSION['mensaje_error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_login.css">
    <link rel="icon" type="image/png" href="../Img/logoo.png">
</head>
<body>

    <main class="main-registro">
        
        <?php if (!empty($mensaje_cuerpo)): ?>
                <div id="customModal" class="modal modal-<?php echo $tipo_alerta; ?>">
                    <div class="modal-content">
                        <span class="modal-close">&times;</span>
                        <div class="modal-icon">
                            <?php if ($tipo_alerta == "exito"): ?>
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php endif; ?>
                        </div>
                        <h2 class="modal-title"><?php echo $mensaje_titulo; ?></h2>
                        <p class="modal-body"><?php echo $mensaje_cuerpo; ?></p>
                        <button class="modal-button" onclick="cerrarModal()">OK</button>
                    </div>
                </div>
            <?php endif; ?>

        <div class="form__logo-container">
            <img src="../Img/user.png" alt="logo" class="user_logo">
        </div>
        <h1 id="h1">REGISTRO DE NUEVO USUARIO</h1>

        <form action="insert_users.php" method="POST" class="grid-form">
            
            <input type="hidden" name="cargo" value="Cliente">

            <div class="form_input">
                <label for="user">Usuario</label>
                <input type="text" name="username" id="user" placeholder="Nombre de usuario" required maxlength="15" autocomplete="off">
            </div>

            <div class="form_input grid-area-pass">
                <label for="pass">Contraseña</label>
                
                <div class="password-container">
                    <input type="password" name="passwordd" id="pass" autocomplete="new-password" placeholder="password" required>
                    
                    <div class="show-password-box">
                        <input type="checkbox" id="show-pass-checkbox" onchange="togglePasswordVisibility()">
                        </div>
                </div>
            </div>

            <div class="form_input">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="nombre" placeholder="Ej. Santiago" required>
            </div>
            
            <div class="form_input">
                <label for="lastname">Apellido</label>
                <input type="text" id="lastname" name="apellido" placeholder="Ej. Carrillo" required>
            </div>

            <div class="form_input">
                <label for="dni">Cédula</label>
                <input type="text" id="dni" name="cedula" placeholder="V-00.000.000" required>
                <small id="dni-help" style="color: #fff; display: none; font-size: 11px;">⚠️ Usa puntos: V-12.345.678</small>
            </div>

            <div class="form_input">
                <label for="fecha_nac">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nac" required>
            </div>

            <div class="form_input">
                <label for="tel">Teléfono</label>
                <input type="text" id="tel" name="telefono" placeholder="0412-0000000" maxlength="12" required>
            </div>

            <div class="form_input">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="email" placeholder="usuario@correo.com" required>
            </div>

            <input type="submit" class="btn__enviar" value="REGISTRARME" onclick="return confirm('¿Desea completar su registro?')">
            
            <div class="register-link" style="grid-column: span 2; text-align: center;">
                <p>¿Ya tienes cuenta? <a href="login.php" style="color: #fff; font-weight: bold;">Inicia sesión aquí</a></p>
            </div>
        </form>
    </main>

    <footer>
        <div>© 2025 OdontoZulia, S.A. Todos los derechos reservados.</div>
    </footer>

    <script>
        
        function cerrarModal() {
            const modal = document.getElementById('customModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('customModal');
            if (modal) {
                // Mostrar el modal si existe un mensaje
                modal.style.display = 'block';

                // Agregar listener al botón 'x'
                const span = document.getElementsByClassName("modal-close")[0];
                if (span) {
                    span.onclick = cerrarModal;
                }
            }
        });

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('pass');
            const checkbox = document.getElementById('show-pass-checkbox');
            if (checkbox.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
        const dniInput = document.getElementById('dni');
        const dniHelp = document.getElementById('dni-help');

        dniInput.addEventListener('focus', function() {
            dniHelp.style.display = 'block';
        });

        dniInput.addEventListener('blur', function() {
            dniHelp.style.display = 'none';
            
            let value = this.value;
            if (value.length > 0 && value.length < 3) {
                this.value = "";
            }
        });

        dniInput.addEventListener('input', function (e) {
            let cursorPosition = e.target.selectionStart;
            let value = e.target.value.toUpperCase();
            
            if (value.length > 0 && !/^[VE]/.test(value)) {
                value = ""; 
            }

            if (value.length === 1 && (value === 'V' || value === 'E')) {
                value = value + "-";
                cursorPosition = 3;
            }

            let prefix = value.substring(0, 2);
            let numberPart = value.substring(2).replace(/[^0-9.]/g, '');
            
            e.target.value = prefix + numberPart;
            e.target.setSelectionRange(cursorPosition, cursorPosition);
        });

        // --- LÓGICA DE TELÉFONO ---
        document.getElementById('tel').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9-]/g, '');
        });
    </script>
</body>
</html>