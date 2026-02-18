<?php
session_start();
require '../../../connection.php';
$con = connection();

$login_url = "../../../login/login.php"; 
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

// Consulta todas las solicitudes que están en espera de revisión
$sql = "SELECT id_solicitud, nombre, cedula, fecha_cita, hora_cita, odontologo, motivo, estado_revision 
        FROM citas_pendientes 
        WHERE estado_revision = 'ESPERA' 
        ORDER BY fecha_cita ASC, hora_cita ASC";

$resultado = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECRETARIA | ODONTOZULIA</title>
        <link rel="icon" type="image/png" href="../../../Img/logoo.png">
    <link href="../../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles_admincita.css">
        <link rel="icon" type="image/png" href="../../../Img/logoo.png">
    <style>
        .status-badge.espera { background-color: #ffc107; color: #000; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .btn-validar { background-color: #28a745; color: white; border: none; }
        .btn-validar:hover { background-color: #218838; color: white; }
        .btn-rechazar { background-color: #dc3545; color: white; border: none; }
        .btn-rechazar:hover { background-color: #c82333; color: white; }
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

            <h1 class="edit__h1">SOLICITUDES DE CITAS POR VALIDAR</h1>

            <div class="table-container mt-4">
                <table class="table_main">
                    <thead>
                        <tr class="tr">
                            <th>Paciente</th>
                            <th>Cédula</th>
                            <th>Fecha Solicitada</th>
                            <th>Hora</th>
                            <th>Odontólogo</th>
                            <th>Motivo</th>
                            <th>Estatus</th>
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
                                    <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                                    <td>
                                        <span class="status-badge espera">POR VALIDAR</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="validar_solicitud.php?id=<?php echo $row['id_solicitud']; ?>" 
                                                class="btn btn-sm btn-validar" 
                                                title="Validar y Agendar"
                                                onclick="return confirm('¿Desea validar esta solicitud y agendarla oficialmente?')">
                                                Validar
                                            </a>
                                            <a href="rechazar_solicitud.php?id=<?php echo $row['id_solicitud']; ?>" 
                                                class="btn btn-sm btn-rechazar" 
                                                title="Rechazar Solicitud"
                                                onclick="return confirm('¿Está seguro de rechazar esta solicitud?')">
                                                Rechazar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center p-4">No hay solicitudes pendientes de validación.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <footer>
        <div>© 2025 OdontoZulia, S.A. Todos los derechos reservados.</div>
    </footer>
</body>
</html>