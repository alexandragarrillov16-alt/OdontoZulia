<?php
session_start();
include('../../../connection.php'); 

$login_url = "../../../login/login.php"; 

// 1. Verificación de sesión por CARGO
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Admin') {
    header("Location: ../../../index.php"); 
    exit();
}

$con = connection();

// 2. Captura del ID del usuario a editar desde la URL
$id_usuario = $_GET['id'] ?? '';

if (empty($id_usuario)) {
    header("Location: admin_userSearch.php");
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($resultado);

if (!$user) {
    echo "Usuario no encontrado.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | ODONTOZULIA</title>
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
            <div>
                <form action="update_user.php" method="POST" class="grid-form form-edit">

                    <h1 class="edit__h1 grid-area-titulo">EDITAR USUARIO (ID: <?php echo $user['username']; ?>)</h1>
                    
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                    <div class="form_input grid-area-user">
                        <label>Nombre de Usuario</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form_input grid-area-pass">
                        <label for="pass">Nueva Contraseña</label>
                        
                        <div class="password-container">
                            <input type="password" name="passwordd" id="pass" autocomplete="new-password">
                            
                            <div class="show-password-box">
                                <input type="checkbox" id="show-pass-checkbox" onchange="togglePasswordVisibility()">
                            </div>
                        </div>
                    </div>

                    <div class="form_input grid-area-nombre">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                    </div>

                    <div class="form_input grid-area-apellido">
                        <label>Apellido</label>
                        <input type="text" name="apellido" value="<?php echo htmlspecialchars($user['apellido']); ?>" required>
                    </div>

                    <div class="form_input grid-area-cedula">
                        <label>Cédula</label>
                        <input type="text" name="cedula" id="dni" value="<?php echo htmlspecialchars($user['cedula']); ?>" required>
                    </div>

                    <div class="form_input grid-area-email">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form_input grid-area-tel">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>" required>
                    </div>

                    <div class="form_input grid-area-fecha">
                        <label>Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="<?php echo $user['fecha_nacimiento']; ?>" required>
                    </div>

                    <div class="form_input grid-area-cargo">
                        <label>Cargo</label>
                        <select name="cargo" required>
                            <option value="Admin" <?php echo ($user['cargo'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="Odontologo" <?php echo ($user['cargo'] == 'Odontologo') ? 'selected' : ''; ?>>Odontologo</option>
                            <option value="Secretaria" <?php echo ($user['cargo'] == 'Secretaria') ? 'selected' : ''; ?>>Secretaria</option>
                            <option value="Cliente" <?php echo ($user['cargo'] == 'Cliente') ? 'selected' : ''; ?>>Cliente</option>
                        </select>
                    </div>

                    <input type="submit" class="btn__enviar grid-area-enviar" value="ACTUALIZAR DATOS" onclick="return confirm('¿Estás seguro que desea actualizar los datos de este usuario?')">
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

        document.getElementById('dni').addEventListener('input', function (e) {
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

        document.getElementById('dni').addEventListener('blur', function (e) {
            let value = e.target.value;
            if (value.length > 0 && value.length < 3) {
                e.target.value = "";
            }
        });
    </script>
</body>
</html>