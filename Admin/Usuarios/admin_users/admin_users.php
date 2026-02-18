<?php
// 1. Lógica PHP para capturar el mensaje de la sesión
session_start();

// 2. Se define la URL de redirección si falla la autenticación 
$login_url = "../../../login/login.php"; 

// 3. Se verifica la autenticación

if (!isset($_SESSION['user_id'])) {
    
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Admin') {
    header("Location: ../../../user/user.php");
    exit();
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
    <title>ADMIN | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_adminusers.css">
    <link rel="icon" type="image/png" href="../../../Img/logoo.png">
</head>
<body>
    <header class="header">
        <div class="header__logo-container">
            <img src="../../../Img/LogoCompleto.png" alt="logo" class="header__logo">
        </div>
        <div class="user-dropdown-container">
        <input type="checkbox" id="open__button">
        <label for="open__button" class="header__open-nav-button" role="button"> 
            <img src="../../../Img/usuario.png" class="header__user-logo">
            <?php 
                $nombreUsuario = $_SESSION['username'] ?? 'USUARIO';
                echo htmlspecialchars(strtoupper($nombreUsuario)); 
            ?>
        </label>
        <nav class="header__nav">
            <ul class="header__nav-list">
                <li class="header__nav-item"><a href="../../logout.php">Salir</a></li>
            </ul>
        </nav>
        </div>
    </header>
    <div class="grid">
    <nav class="nav__search">
        <ul>
            <details>
                <summary>
                    <img src="../../../Img/paciente.png" alt="" class="menu-icon">
                    Gestión De Pacientes
                </summary>
                <li><a href="/Admin/Pacientes/admin_agg/admin_agg.php">Registrar Paciente</a></li>
                <li><a href="/Admin/Pacientes/admin_search/admin_search.php">Busqueda Avanzada</a></li>
            </details>

            <details>
                <summary>
                    <img src="../../../Img/citas.png" alt="" class="menu-icon">
                    Agenda De Citas
                </summary>
                <li><a href="/Admin/Citas/admin_agendar/admin_searchcita.php">Agendar Cita</a></li>
                <li><a href="/Admin/Citas/admin_consulta/admin_consultar.php">Consultar Citas</a></li>
                <li><a href="/Admin/Citas/admin_solicitudes/validar_citas.php">Validar Citas Online</a></li>
            </details>

            <details>
                <summary>
                    <img src="../../../Img/historia.png" alt="" class="menu-icon">
                    Historia Clínica Electrónica
                </summary>
                <li><a href="/Admin/HistorialClinico/admin_crearHistoria/admin_fichaSearch.php">Registrar Historia Clínica</a></li>
                <li><a href="/Admin/HistorialClinico/admin_consultarHistoria/admin_historySearch.php">Gestión de Historial Clínico</a></li>
            </details>

            <details>
                <summary>
                    <img src="../../../Img/useragg.png" alt="" class="menu-icon">
                    Gestión De Usuarios
                </summary>
                <li><a href="/Admin/Usuarios/admin_users/admin_users.php">Usuarios</a></li>
                <li><a href="/Admin/Usuarios/consultar_usuarios/admin_userSearch.php">Consultar Usuarios</a></li>
            </details>

            <details>
                <summary>
                    <img src="../../../Img/reporte.png" alt="" class="menu-icon">
                    Reportes
                </summary>
                <li><a href="/Admin/Reportes/admin_report/admin_report.php">Reportes</a></li>
            </details>
        </ul>
    </nav>
    <main class="main">

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

        <div>
            <form action="insert_users.php" method="POST" class="grid-form form-basicos">
                
                <div class="form__logo-container grid-area-img">
                    <img src="../../../Img/user.png" alt="logo" class="user_logo">
                </div>
                <h1 id="h1" class="grid-area-titulo">CREAR USUARIO</h1>

                <div class="form_input grid-area-user" >
                    <label for="user">Usuario</label>
                    <input type="text" name="username" id="user" autocomplete="off" maxlength="15" placeholder="username" required>
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

                <div class="form_input grid-area-nombre" >
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="nombre" placeholder="Ej..Santiago" required>
                </div>
                
                <div class="form_input grid-area-apellido">
                    <label for="lastname">Apellido</label>
                    <input type="text" id="lastname" name="apellido" placeholder="Ej..Garrillo" required>
                </div>

                <div class="form_input grid-area-cedula">
                    <label for="dni">Cédula</label>
                    <input type="text" id="dni" name="cedula" placeholder="V-00.000.000" required>
                    <small id="dni-help" style="color: #004080; display: none; margin-top: 5px; font-weight: bold;">
                        ⚠️ Recuerda separar por puntos los números.
                    </small>
                </div>

                <div class="form_input grid-area-fecha">
                    <label for="fecha_nac">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nac" required>
                </div>

                <div class="form_input grid-area-telefono">
                    <label for="tel">Teléfono</label>
                    <input type="text" id="tel" name="telefono" placeholder="0412-0000000" maxlength="12" required>
                </div>

                <div class="form_input grid-area-email">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="email" placeholder="Odontozulia@gmail.com" required>
                </div>

                <div class="form_input grid-area-cargo">
                    <label for="carg">Cargo</label>
                    <select id="carg" name="cargo" required onchange="toggleHorario()">
                        <option value="" selected disabled hidden>-- Por favor, selecciona una opción --</option>
                        <option value="Admin">Admin</option>
                        <option value="Odontologo">Odontologo</option>
                        <option value="Secretaria">Secretaria</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                </div>

                <div id="contenedor-horario">
                    <label>Definir Jornada Laboral</label>
                    <div >
                        <div class="form_input" style="flex: 1;">
                            <label for="hora_inicio">Hora de Entrada</label>
                            <input type="time" id="hora_inicio" name="hora_inicio">
                        </div>
                        <div style="margin-top: 25px; font-weight: bold;">HASTA</div>
                        <div class="form_input" style="flex: 1;">
                            <label for="hora_fin">Hora de Salida</label>
                            <input type="time" id="hora_fin" name="hora_fin">
                        </div>
                    </div>
                </div>

                <input type="submit" role="button" class="btn__enviar grid-area-enviar" value="CREAR" onclick="return confirm('¿Estás seguro que desea agregar a este usuario?')">
            </form>
        </div>
        </main>
    </div>
    <footer>
        <div>
        © 2025 OdontoZulia, S.A. Todos los derechos reservados.
        </div>
    </footer>

    <script>

        function toggleHorario() {
            const cargoSelect = document.getElementById('carg');
            const contenedorHorario = document.getElementById('contenedor-horario');
            const inputInicio = document.getElementById('hora_inicio');
            const inputFin = document.getElementById('hora_fin');

            if (cargoSelect.value === 'Odontologo') {
                contenedorHorario.style.display = 'block';
                inputInicio.required = true;
                inputFin.required = true;
        } else {
                contenedorHorario.style.display = 'none';
                inputInicio.required = false;
                inputFin.required = false;
                inputInicio.value = "";
                inputFin.value = "";
            }
        }

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

            // Si el checkbox está marcado, cambia el tipo de 'password' a 'text'.
            // Si no está marcado, vuelve a cambiarlo de 'text' a 'password'.
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
    </script>
</body>
</html>