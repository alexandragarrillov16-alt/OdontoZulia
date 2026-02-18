<?php
session_start();
if (isset($_SESSION['login_error'])) {
    echo '<div class="alerta-error">' . $_SESSION['login_error'] . '</div>';
    unset($_SESSION['login_error']);
}

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
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles_login.css">
    <link rel="icon" type="image/png" href="../Img/logoo.png">
</head>
<body>
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

    <div class="body__wrapper">
        <form action="logindata.php" method="POST">
            <h1>Inicio de Sesión</h1>
            <div class="input-box">
                <input type="text" placeholder="" required id="nombreUsuario" name="username">
                <label for="nombreUsuario">Nombre de Usuario</label>
                <img src="user_icon.png" width="25px" class="img-icon">
            </div>
            <div class="input-box">
                <input type="password" placeholder="" id="pass" required name="passwordd">
                <label for="pass">Contraseña</label>
                <img src="pass_icon.png" width="25px" class="img-icon">
            </div>
            <div class="remember-forgot">
                <a href="recuperar.php">¿Olvidó su contraseña? Click Aqui</a>
            </div>
            <button type="submit" class="btn__enviar">Ingresar</button>
            <div class="register-link">
                <p>¿No tiene una cuenta?<a href="registro.php"> Registrese aqui</a></p>
            </div>
        </form>
    </div>
    <footer>
        <div>
        © 2025 OdontoZulia, S.A. Todos los derechos reservados.
        </div>
    </footer>
    <script>
        // Función para cerrar el modal
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
    </script>
</body>
</html>