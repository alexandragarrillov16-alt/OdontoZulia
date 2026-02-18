<?php
session_start();
require '../../../connection.php';
$con = connection();

$login_url = "../../../login/login.php"; 
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Odontologo') {
    header("Location: ../../../Login/login.php");
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
    <title>ODONTOLOGO | ODONTOZULIA</title>
        <link rel="icon" type="image/png" href="../../../Img/logoo.png">
    <link href="../../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles_cita.css">
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
                    <img src="../../../Img/citas.png" alt="" class="menu-icon">
                    Agenda De Citas
                </summary>
                <li><a href="/Odontologo/Citas/consulta/consultar.php">Consultar Citas</a></li>
            </details>

            <details>
                <summary>
                    <img src="../../../Img/historia.png" alt="" class="menu-icon">
                    Historia Clínica Electrónica
                </summary>
                <li><a href="/Odontologo/HistorialClinico/crearHistoria/fichaSearch.php">Registrar Historia Clínica</a></li>
                <li><a href="/Odontologo/HistorialClinico/consultarHistoria/historySearch.php">Gestión de Historial Clínico</a></li>
            </details>
        </ul>
    </nav>

    <main class="main">
            <?php if(isset($_SESSION['mensaje_exito'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
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
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Odontólogo</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row['fecha_cita'])); ?></td>
                                    <td><strong><?php echo date("h:i A", strtotime($row['hora_cita'])); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['odontologo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $row['estatus']; ?>">
                                            <?php echo $row['estatus']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="td">No hay citas agendadas para esta fecha.</td>
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