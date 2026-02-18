<?php
session_start();

$login_url = "../../../login/login.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Secretaria') {
    header("Location: ../../../Login/login.php");
    exit();
}

// Función de seguridad para parámetros URL
function get_url_param($param_name) {
    return $_GET[$param_name] ?? ''; 
}

// --- Captura de datos del Paciente desde la URL ---
$nro_historia = get_url_param('nro_historia');
$codigo_paciente = get_url_param('codigo_paciente');
$nombre = get_url_param('nombre');
$cedula = get_url_param('cedula');
$fecha_nacimiento = get_url_param('fecha_nacimiento');
$sexo = get_url_param('sexo');
$ocupacion = get_url_param('ocupacion');
$direccion = get_url_param('direccion');
$telefono = get_url_param('telefono');
$email = get_url_param('email');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECRETARIA | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_adminsearch.css">
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
                <li><a href="/Secretaria/Pacientes/pacientes_agg/agg.php">Registrar Paciente</a></li>
                <li><a href="/Secretaria/Pacientes/pacientes_search/search.php">Busqueda Avanzada</a></li>
            </details>

            <details>
                <summary>
                    <img src="../../../Img/citas.png" alt="" class="menu-icon">
                    Agenda De Citas
                </summary>
                <li><a href="/Secretaria/Citas/agendar/searchcita.php">Agendar Cita</a></li>
                <li><a href="/Secretaria/Citas/consulta/consultar.php">Consultar Citas</a></li>
                <li><a href="/Secretaria/Citas/solicitudes/validar_citas.php">Validar Citas Online</a></li>
            </details>
        </ul>
    </nav>

        <main class="main">
            <div>
                <form action="update_paciente.php" method="POST" class="grid-form form-edit">

                    <h1 class="edit__h1">EDITAR PACIENTE (ID: <?php echo htmlspecialchars($codigo_paciente); ?>)</h1>
                    
                    <input type="hidden" name="nro_historia" value="<?php echo htmlspecialchars($nro_historia); ?>">
                    <input type="hidden" name="codigo_paciente" value="<?php echo htmlspecialchars($codigo_paciente); ?>">

                    <div class="form_input grid-area-nombre">
                        <label for="name">Nombre Completo</label>
                        <input type="text" name="nombre" id="name" 
                            value="<?php echo htmlspecialchars($nombre); ?>" required>
                    </div>

                    <div class="form_input grid-area-cedula">
                        <label for="ced">Cédula de Identidad</label>
                        <input type="text" name="cedula" id="ced" 
                            value="<?php echo htmlspecialchars($cedula); ?>" required>
                    </div>

                    <div class="form_input grid-area-fecha">
                        <label for="fecha_nac">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nac" 
                            value="<?php echo htmlspecialchars($fecha_nacimiento); ?>" required>
                    </div>

                    <div class="form_input grid-area-sexo">
                        <label for="genero">Sexo</label>
                        <select id="genero" name="sexo" required>
                            <option value="M" <?php echo ($sexo == 'M') ? 'selected' : ''; ?>>M</option>
                            <option value="F" <?php echo ($sexo == 'F') ? 'selected' : ''; ?>>F</option>
                        </select>
                    </div>

                    <div class="form_input grid-area-ocu">
                        <label for="ocu">Ocupación</label>
                        <input type="text" name="ocupacion" id="ocu" 
                            value="<?php echo htmlspecialchars($ocupacion); ?>" required>
                    </div>

                    <div class="form_input grid-area-direc">
                        <label for="direc">Dirección de Habitación</label>
                        <input type="text" name="direccion" id="direc" 
                            value="<?php echo htmlspecialchars($direccion); ?>" required>
                    </div>

                    <div class="form_input grid-area-tel">
                        <label for="tel">Teléfono</label>
                        <input type="text" name="telefono" id="tel" 
                            value="<?php echo htmlspecialchars($telefono); ?>" required>
                    </div>

                    <div class="form_input grid-area-email">
                        <label for="correo">Correo Electrónico</label>
                        <input type="text" name="email" id="correo" 
                            value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <input type="submit" role="button" class="btn__enviar grid-area-enviar" value="GUARDAR" onclick="return confirm('¿Está seguro que desea actualizar estos datos?')">
                </form>
            </div>
        </main>
    </div>

    <footer>
        <div>
            © 2025 OdontoZulia, S.A. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>