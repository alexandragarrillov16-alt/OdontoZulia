<?php

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
    <link rel="stylesheet" href="styles_adminagg.css">
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
    
        <div id="formulario_basicos">
            
            <h1 id="h1">FICHA MEDICA</h1>

            <form action="insert_pacientes.php" method="POST" class="grid-form form-basicos"> 

                    <div class="form_input grid-area-nombre">
                        <label for="name">Nombre Completo</label>
                        <input type="text" name="nombre" id="name" placeholder="Ej. Carlos Rodriguez" required>
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

                    <div class="form_input grid-area-sexo">
                        <label for="genero">Sexo</label>
                        <select id="genero" name="sexo" required>
                            <option value="" selected disabled hidden>-- Por favor, selecciona una opción --</option>
                            <option value="M">M</option>
                            <option value="F">F</option>
                        </select>
                    </div>

                    <div class="form_input grid-area-ocu">
                        <label for="ocu">Ocupación</label>
                        <input type="text" name="ocupacion" id="ocu" placeholder="Ej. Obrero, maquinista, etc..." required>
                    </div>

                    <div class="form_input grid-area-direc">
                        <label for="direc">Dirección de habitación</label>
                        <input type="text" name="direccion" id="direc" placeholder="Ej. Estado, Municipio, Parroquia y Sector..." required>
                    </div>

                    <div class="form_input grid-area-tel">
                        <label for="tel">Teléfono Celular / Local</label>
                        <input type="text" name="telefono" id="tel" placeholder="Ej. 0424-0000000" maxlength="12" required>
                    </div>

                    <div class="form_input grid-area-email">
                        <label for="correo">Correo Electrónico</label>
                        <input type="text" name="email" id="correo" placeholder="Ej. carlos@gmail.com" required>
                    </div>
                
                <input type="submit" role="button" class="btn__enviar grid-area-enviar" onclick="return confirm('¿Estás seguro que desea agregar a este paciente?')">
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

                modal.style.display = 'block';

                const span = document.getElementsByClassName("modal-close")[0];
                if (span) {
                    span.onclick = cerrarModal;
                }
            }
        });

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