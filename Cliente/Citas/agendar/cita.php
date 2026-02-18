<?php
session_start();
require '../../../connection.php';
$con = connection();

$login_url = "../../../login/login.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Cliente') {
    header("Location: ../../../Login/login.php");
    exit();
}

// --- Obtener datos del usuario logueado ---
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT nombre, cedula, telefono, email FROM users WHERE id = ?";
$stmt = $con->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res_user = $stmt->get_result();
$datos_usuario = $res_user->fetch_assoc();

$nombre = $datos_usuario['nombre'] ?? 'Usuario';
$cedula = $datos_usuario['cedula'] ?? '';
$telefono = $datos_usuario['telefono'] ?? '';
$email = $datos_usuario['email'] ?? '';

$has_active_appointment = false;
$msg_bloqueo = "";

// 1. Verificar en tabla de citas reales
$check_cita = $con->prepare("SELECT id_cita FROM citas WHERE cedula = ? AND estatus = 'PENDIENTE'");
$check_cita->bind_param("s", $cedula);
$check_cita->execute();
if($check_cita->get_result()->num_rows > 0) {
    $has_active_appointment = true;
    $msg_bloqueo = "⚠️ Ya posees una cita confirmada. No puedes agendar otra hasta asistir a la actual.";
}

if (!$has_active_appointment) {
    $check_pendiente = $con->prepare("SELECT id_solicitud FROM citas_pendientes WHERE cedula = ? AND estado_revision = 'ESPERA'");
    $check_pendiente->bind_param("s", $cedula);
    $check_pendiente->execute();
    if($check_pendiente->get_result()->num_rows > 0) {
        $has_active_appointment = true;
        $msg_bloqueo = "⏳ Tienes una solicitud en espera de validación por la secretaria. Debes esperar respuesta.";
    }
}

$sql_medicos = "SELECT nombre, apellido, horario_trabajo FROM users WHERE cargo = 'Odontologo'";
$res_medicos = mysqli_query($con, $sql_medicos); 

$agenda_ocupada = [];

$res1 = $con->query("SELECT fecha_cita, hora_cita, odontologo FROM citas WHERE estatus = 'PENDIENTE'");
while($row = $res1->fetch_assoc()){
    $agenda_ocupada[$row['fecha_cita']][$row['odontologo']][] = substr($row['hora_cita'], 0, 5);
}

$res2 = $con->query("SELECT fecha_cita, hora_cita, odontologo FROM citas_pendientes WHERE estado_revision = 'ESPERA'");
while($row = $res2->fetch_assoc()){
    $agenda_ocupada[$row['fecha_cita']][$row['odontologo']][] = substr($row['hora_cita'], 0, 5);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLIENTE | ODONTOZULIA</title>
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
                    <img src="../../../Img/citas.png" alt="" class="menu-icon">
                    Agenda De Citas
                </summary>
                <li><a href="/Cliente/Citas/agendar/cita.php">Agendar Cita</a></li>
                <li><a href="/Cliente/Citas/consulta/consultar.php">Consultar Cita</a></li>
            </details>
            <details>
                <summary>
                    <img src="../../../Img/historia.png" alt="" class="menu-icon">
                    Historia Clínica Electrónica
                </summary>
                <li><a href="/Cliente/HistorialClinico/consultarHistoria/historySearch.php">Mi Historial Clínico</a></li>
            </details>
        </ul>
    </nav>

<main class="main">

            <div class="calendar-container shadow-sm p-4 bg-white rounded">
                <h1 class="edit__h1 text-center">AGENDAR CITA ODONTOLÓGICA</h1>

                <div class="info-paciente alert alert-info mt-4">
                    <strong>Paciente:</strong> <?php echo htmlspecialchars($nombre); ?> <br>
                    <strong>Cédula:</strong> <?php echo htmlspecialchars($cedula); ?>
                </div>

                <form action="save_cita.php" method="POST" class="grid-form mt-4" id="formCita">
                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
                    <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($cedula); ?>">
                    <input type="hidden" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                    <div class="form_input">
                        <label>Seleccione un Odontólogo</label>
                        <select name="odontologo" id="odontologo" required onchange="generarHorasDisponibles()">
                            <option value="" selected disabled hidden>-- Seleccione un Odontólogo --</option>
                            <?php while($medico = mysqli_fetch_assoc($res_medicos)): 
                                $nombreCompleto = $medico['nombre'] . " " . $medico['apellido']; ?>
                                <option value="<?php echo $nombreCompleto; ?>" data-horario="<?php echo $medico['horario_trabajo']; ?>">
                                    <?php echo $nombreCompleto; ?> (<?php echo $medico['horario_trabajo']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form_input">
                        <label for="fecha_cita">Seleccione Fecha</label>
                        <input type="date" name="fecha_cita" id="fecha_cita" class="form-control" min="<?php echo date('Y-m-d'); ?>" required onchange="validarDisponibilidad()">
                    </div>
                
                    <div class="form_input">
                        <label for="hora_cita">Seleccione Hora</label>
                        <select name="hora_cita" id="hora_cita" class="form-select" required onchange="validarDisponibilidad()">
                            <option value="" selected disabled hidden>Primero seleccione un odontólogo...</option>
                        </select>
                    </div>

                    <div class="form_input w-100 mt-3" style="grid-column: span 2;">
                        <span id="msg-disponibilidad" class="fw-bold"></span>
                    </div>

                    <div class="form_input mt-3" style="grid-column: span 2;">
                        <label for="motivo">Motivo de la Cita</label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3" required placeholder="Ej. Evaluación inicial..."></textarea>
                    </div>

                    <div class="text-center mt-4" style="grid-column: span 2;">
                        <input type="submit" class="btn btn-primary btn-lg w-50" value="SOLICITAR CITA" id="btn-submit" onclick="return confirm('¿Desea solicitar esta cita?')">
                    </div>
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
        const agendaOcupada = <?php echo json_encode($agenda_ocupada); ?>;
        const tieneCitaActiva = <?php echo $has_active_appointment ? 'true' : 'false'; ?>;
        const mensajeBloqueo = "<?php echo $msg_bloqueo; ?>";

        function generarHorasDisponibles() {
            const odontologoSelect = document.getElementById('odontologo');
            const horaSelect = document.getElementById('hora_cita');
            const selectedOption = odontologoSelect.options[odontologoSelect.selectedIndex];
            const horarioRaw = selectedOption.getAttribute('data-horario');

            horaSelect.innerHTML = '<option value="" selected disabled hidden>Seleccione hora...</option>';
            if (!horarioRaw) return;

            const partes = horarioRaw.split(" - ");
            const inicio = parseInt(partes[0].split(":")[0]);
            const fin = parseInt(partes[1].split(":")[0]);

            for (let h = inicio; h < fin; h++) {
                let horaFormateada = h < 10 ? `0${h}:00` : `${h}:00`;
                let label = h < 12 ? `${h}:00 AM` : (h === 12 ? `12:00 PM` : `${h-12}:00 PM`);
                
                let option = document.createElement('option');
                option.value = horaFormateada;
                option.text = label;
                horaSelect.appendChild(option);
            }
            validarDisponibilidad();
        }

        function validarDisponibilidad() {
            const msg = document.getElementById('msg-disponibilidad');
            const btn = document.getElementById('btn-submit');
            
            if (tieneCitaActiva) {
                msg.innerHTML = mensajeBloqueo;
                msg.style.color = "#856404";
                btn.disabled = true;
                btn.style.opacity = "0.5";
                return;
            }

            const fecha = document.getElementById('fecha_cita').value;
            const hora = document.getElementById('hora_cita').value;
            const doctor = document.getElementById('odontologo').value;

            msg.innerHTML = "";
            btn.disabled = false;
            btn.style.opacity = "1";

            if (!fecha || !hora || !doctor) return;

            if (agendaOcupada[fecha] && agendaOcupada[fecha][doctor] && agendaOcupada[fecha][doctor].includes(hora)) {
                msg.innerHTML = `❌ El Dr. ${doctor} ya tiene una cita (o solicitud pendiente) a las ${hora} el día ${fecha}.`;
                msg.style.color = "red";
                btn.disabled = true;
                btn.style.opacity = "0.5";
            } else {
                msg.innerHTML = "✅ Horario disponible.";
                msg.style.color = "green";
            }
        }

        window.onload = validarDisponibilidad;
    </script>

</body>
</html>