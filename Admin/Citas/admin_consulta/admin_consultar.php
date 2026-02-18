<?php
session_start();
require '../../../connection.php';
$con = connection();

$login_url = "../../../login/login.php"; 
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$sql = "SELECT id_cita, nombre, cedula, telefono, email, fecha_cita, hora_cita, odontologo, motivo, estatus 
        FROM citas 
        WHERE fecha_cita = '$fecha_filtro' 
        ORDER BY hora_cita ASC";

$resultado = $con->query($sql);
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
            <?php if(isset($_SESSION['mensaje_exito'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['mensaje_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                </div>
            <?php endif; ?>

            <h1 class="edit__h1">CONTROL DE CITAS AGENDADAS</h1>

            <div class="filter-container">
                <form method="GET" action="" class="form-filter">
                    <label>Filtrar por Fecha:</label>
                    <input type="date" name="fecha" value="<?php echo $fecha_filtro; ?>" class="form_input">
                    <button type="submit" class="btn__enviar">VER CITAS</button>
                </form>
            </div>

            <div class="table-container">
                <table class="table_main">
                    <thead>
                        <tr class="tr">
                            <th>Paciente</th>
                            <th>Cédula</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Odontólogo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row['fecha_cita'])); ?></td>
                                    <td><strong><?php echo date("h:i A", strtotime($row['hora_cita'])); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['odontologo']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $row['estatus']; ?>">
                                            <?php echo $row['estatus']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="finalizar_cita.php?id=<?php echo $row['id_cita']; ?>" class="btn btn-sm btn1" title="Finalizar" onclick="return confirm('¿Desea finalizar esta cita?')">✓</a>
                                        
                                        <button type="button" class="btn btn-sm btn-info" 
                                                style="background-color: #17a2b8; color: white; border: none;"
                                                onclick="abrirModalReagendar('<?php echo $row['id_cita']; ?>', '<?php echo $row['nombre']; ?>', '<?php echo $row['fecha_cita']; ?>', '<?php echo $row['hora_cita']; ?>')" 
                                                title="Reagendar">🔄</button>

                                        <a href="cancelar_cita.php?id=<?php echo $row['id_cita']; ?>" class="btn btn-sm btn2" title="Cancelar" onclick="return confirm('¿Desea cancelar esta cita?')">✕</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="td">No hay citas agendadas para esta fecha.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

        <div class="modal fade" id="modalReagendar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form action="reagendar_cita.php" method="POST">
                <div class="modal-header">
                <h5 class="modal-title">Reagendar Cita: <span id="nombrePacienteModal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_cita" id="modal_id_cita">
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Fecha</label>
                        <input type="date" name="nueva_fecha" id="modal_fecha" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Hora</label>
                        <input type="time" name="nueva_hora" id="modal_hora" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
            </div>
        </div>
        </div>

    <footer>
        <div>© 2025 OdontoZulia, S.A. Todos los derechos reservados.</div>
    </footer>

    <script src="../../../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModalReagendar(id, nombre, fecha, hora) {
            document.getElementById('modal_id_cita').value = id;
            document.getElementById('nombrePacienteModal').innerText = nombre;
            document.getElementById('modal_fecha').value = fecha;
            document.getElementById('modal_hora').value = hora;
            
            var myModal = new bootstrap.Modal(document.getElementById('modalReagendar'));
            myModal.show();
        }
    </script>
</body>
</html>