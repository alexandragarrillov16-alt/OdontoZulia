<?php
session_start();

$mensaje_titulo = "";
$mensaje_cuerpo = "";
$tipo_alerta = ""; 

if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_titulo = "¡Datos Recuperados! 🔑";
    $mensaje_cuerpo = $_SESSION['mensaje_exito'];
    $tipo_alerta = "exito";
    unset($_SESSION['mensaje_exito']); 
} elseif (isset($_SESSION['mensaje_error'])) { 
    $mensaje_titulo = "¡No se encontró la cuenta! 🚫"; 
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
    <title>Recuperar Cuenta | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_login.css">
    <link rel="icon" type="image/png" href="../Img/logoo.png">
    <style>
        /* Ajuste para que el formulario no sea tan largo como el de registro */
        .main-registro {
            max-width: 450px;
        }
        .recuperar-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #eee;
        }
    </style>
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
        <h1 id="h1">RECUPERAR CUENTA</h1>
        <p class="recuperar-info">Introduce tus datos registrados para recuperar tu acceso.</p>

        <form action="procesar_recuperacion.php" method="POST" class="grid-form">
            
            <div class="form_input" style="grid-column: span 2;">
                <label for="dni">Cédula del Titular</label>
                <input type="text" id="dni" name="cedula" placeholder="V-00.000.000" required autocomplete="off">
                <small id="dni-help" style="color: #fff; display: none; font-size: 11px;">⚠️ Ejemplo: V-12.345.678</small>
            </div>

            <div class="form_input" style="grid-column: span 2;">
                <label for="correo">Correo Electrónico Registrado</label>
                <input type="email" id="correo" name="email" placeholder="usuario@correo.com" required>
            </div>

            <div class="form_input" style="grid-column: span 2;">
                <label for="pass">Nueva Contraseña</label>
                <input type="password" id="pass" name="nueva_password" placeholder="newpassword" autocomplete="new-password" required>
            </div>

            <input type="submit" class="btn__enviar" value="RESTABLECER CONTRASEÑA">
            
            <div class="register-link" style="grid-column: span 2; text-align: center;">
                <p>¿Recordaste tus datos? <a href="login.php" style="color: #fff; font-weight: bold;">Volver al Login</a></p>
            </div>
        </form>
    </main>

    <footer>
        <div>© 2025 OdontoZulia, S.A. Todos los derechos reservados.</div>
    </footer>

    <script>
        function cerrarModal() {
            const modal = document.getElementById('customModal');
            if (modal) modal.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('customModal');
            if (modal) modal.style.display = 'block';
        });

        const dniInput = document.getElementById('dni');
        const dniHelp = document.getElementById('dni-help');

        dniInput.addEventListener('focus', () => { dniHelp.style.display = 'block'; });
        dniInput.addEventListener('blur', () => { dniHelp.style.display = 'none'; });

        dniInput.addEventListener('input', function (e) {
            let cursorPosition = e.target.selectionStart;
            let value = e.target.value.toUpperCase();
            if (value.length > 0 && !/^[VE]/.test(value)) value = ""; 
            if (value.length === 1 && (value === 'V' || value === 'E')) value = value + "-";
            let prefix = value.substring(0, 2);
            let numberPart = value.substring(2).replace(/[^0-9.]/g, '');
            e.target.value = prefix + numberPart;
        });
    </script>
</body>
</html>