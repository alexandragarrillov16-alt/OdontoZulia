<?php
session_start();
require '../../../connection.php';
$con = connection();

$login_url = "../../../login/login.php"; 
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

// 1. Obtener la cédula del usuario logueado
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT cedula FROM users WHERE id = ?"; 
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$datos_usuario = $res_user->fetch_assoc();
$cedula_usuario = $datos_usuario['cedula'] ?? '';

// 2. Consulta Unificada
$sql = "
    (SELECT id_cita AS id, fecha_cita, hora_cita, odontologo, motivo, estatus, 'REAL' AS tipo 
        FROM citas 
        WHERE cedula = ?)
    UNION ALL
    (SELECT id_solicitud AS id, fecha_cita, hora_cita, odontologo, motivo, 'ESPERA DE VALIDACIÓN' AS estatus, 'SOLICITUD' AS tipo 
        FROM citas_pendientes 
        WHERE cedula = ? AND estado_revision = 'ESPERA')
    ORDER BY fecha_cita DESC, hora_cita ASC";

$stmt_citas = $con->prepare($sql);
$stmt_citas->bind_param("ss", $cedula_usuario, $cedula_usuario);
$stmt_citas->execute();
$resultado = $stmt_citas->get_result();

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
        <link rel="icon" type="image/png" href="../../../Img/logoo.png">
    <link href="../../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles_admincita.css">
        <link rel="icon" type="image/png" href="../../../Img/logoo.png">

        <style>
        .status-badge.espera { background-color: #ffc107; color: #000; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-badge.pendiente { background-color: #28a745; color: #fff; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-badge.cancelada { background-color: #dc3545; color: #fff; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
    </style>
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

            <h1 class="edit__h1">ESTADO DE MIS CITAS</h1>

            <div class="table-container">
                <table class="table_main">
                    <thead>
                        <tr class="tr">
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Odontólogo</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", strtotime($row['fecha_cita'])); ?></td>
                                    <td><strong><?php echo date("h:i A", strtotime($row['hora_cita'])); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['odontologo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                                    <td>
                                        <?php 
                                            $clase = ($row['estatus'] == 'ESPERA DE VALIDACIÓN') ? 'espera' : strtolower($row['estatus']);
                                        ?>
                                        <span class="status-badge <?php echo $clase; ?>">
                                            <?php echo htmlspecialchars($row['estatus']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['tipo'] == 'SOLICITUD'): ?>
                                            <a href="cancelar_solicitud.php?id=<?php echo $row['id']; ?>" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('¿Estás seguro de retirar esta solicitud de cita?')">
                                                ✕ Cancelar Solicitud
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted italic small">Cita Procesada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No posees registros de citas o solicitudes.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

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

    <footer>
        <div>© 2025 OdontoZulia, S.A. Todos los derechos reservados.</div>
    </footer>
</body>
</html>