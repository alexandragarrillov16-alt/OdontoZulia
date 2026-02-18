<?php
session_start();
require '../../../connection.php';
$con = connection();

$login_url = "../../../login/login.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Admin') {
    header("Location: ../../../user/user.php");
    exit();
}

// 1. Obtener el código del paciente desde la URL
$codigo_paciente = isset($_GET['codigo_paciente']) ? $_GET['codigo_paciente'] : '';

// 2. Consultar los datos actuales en historia_pacientes
$datos = [];
if (!empty($codigo_paciente)) {
    $stmt = $con->prepare("SELECT * FROM historia_pacientes WHERE codigo_paciente = ? LIMIT 1");
    $stmt->bind_param("s", $codigo_paciente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $stmt->close();
}

$id_fila_actual = $datos['nro_historia'] ?? '';
$nombre = $datos['nombre'] ?? ($_GET['nombre'] ?? '');
$cedula = $datos['cedula'] ?? ($_GET['cedula'] ?? '');
$enf_previas = isset($datos['enfermedades_sistemicas']) ? explode(", ", $datos['enfermedades_sistemicas']) : [];

// Lógica de mensajes de sesión
$mensaje_titulo = ""; $mensaje_cuerpo = ""; $tipo_alerta = ""; 
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_titulo = "¡Carga Exitosa! 🎉"; $mensaje_cuerpo = $_SESSION['mensaje_exito'];
    $tipo_alerta = "exito"; unset($_SESSION['mensaje_exito']); 
} elseif (isset($_SESSION['mensaje_error'])) { 
    $mensaje_titulo = "¡Error en la Operación! 🚫"; $mensaje_cuerpo = $_SESSION['mensaje_error'];
    $tipo_alerta = "error"; unset($_SESSION['mensaje_error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_adminhistory.css">
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
    
        <div id="formulario_historia_clinica">
            <h1 id="h1">EDITAR HISTORIA CLÍNICA ELECTRÓNICA</h1>

            <form action="update_history.php" method="POST" class="grid-form form-especialista"> 

                <input type="hidden" name="nro_historia" value="<?php echo $id_fila_actual; ?>">
                <input type="hidden" name="codigo_paciente" value="<?php echo htmlspecialchars($codigo_paciente); ?>">
                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
                <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($cedula); ?>">

                <fieldset class="form-section grid-area-anamnesis">
                    <legend>A. Anamnesis</legend>
                    <div class="form_input">
                        <label for="motivo">Motivo de Consulta</label>
                        <textarea name="motivo_consulta" id="motivo" rows="2" required><?php echo htmlspecialchars($datos['motivo_consulta'] ?? ''); ?></textarea>
                    </div>
                    <div class="form_input">
                        <label for="enfermedad_actual">Enfermedad Actual</label>
                        <textarea name="enfermedad_actual" id="enfermedad_actual" rows="3" required><?php echo htmlspecialchars($datos['enfermedad_actual'] ?? ''); ?></textarea>
                    </div>
                </fieldset>

                <fieldset class="form-section grid-area-antecedentes">
                    <legend>B. Antecedentes Personales</legend>
                    
                    <div class="form_group_inline">
                        <div class="form_input">
                            <label>¿Padece alguna enfermedad?</label>
                            <div class="checkbox-group">
                                <?php 
                                $opciones_enf = ["Diabetes", "Hipertension", "Cardiopatias", "Asma", "VIH"];
                                foreach($opciones_enf as $opc): 
                                    $checked = in_array($opc, $enf_previas) ? "checked" : "";
                                ?>
                                    <label><?php echo $opc; ?></label>
                                    <input type="checkbox" name="enf[]" value="<?php echo $opc; ?>" <?php echo $checked; ?>> 
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form_input alerta-alergias">
                        <label for="alergias">Alergias</label>
                        <input type="text" name="alergias" id="alergias" value="<?php echo htmlspecialchars($datos['alergias'] ?? ''); ?>" required>
                    </div>

                    <div class="form_input">
                        <label for="habitos">Hábitos</label>
                        <input type="text" name="habitos" id="habitos" value="<?php echo htmlspecialchars($datos['habitos'] ?? ''); ?>" required>
                    </div>

                    <div class="form_input">
                        <label for="embarazo">Embarazo</label>
                        <select name="embarazo" id="embarazo" required>
                            <option value="No" <?php echo ($datos['embarazo'] ?? '') == 'No' ? 'selected' : ''; ?>>No</option>
                            <option value="Si" <?php echo ($datos['embarazo'] ?? '') == 'Si' ? 'selected' : ''; ?>>Sí</option>
                            <option value="Posible" <?php echo ($datos['embarazo'] ?? '') == 'Posible' ? 'selected' : ''; ?>>Posible</option>
                        </select>
                        <input type="number" name="meses_embarazo" value="<?php echo $datos['meses_embarazo'] ?? 0; ?>" min="0" max="9" class="input_meses" required>
                    </div>

                    <div class="form_input">
                        <label for="meds">Medicamentos que consume actualmente</label>
                        <textarea name="medicamentos_actuales" id="meds" rows="2" required><?php echo htmlspecialchars($datos['medicamentos'] ?? ''); ?></textarea>
                    </div>
                </fieldset>

                <fieldset class="form-section grid-area-examen">
                    <legend>C. Examen Clínico Intraoral</legend>
                    <div class="form_input">
                        <label for="mucosa">Estado de la mucosa</label>
                        <input type="text" name="estado_mucosa" id="mucosa" value="<?php echo htmlspecialchars($datos['estado_mucosa'] ?? ''); ?>" required>
                    </div>
                    <div class="form_input">
                        <label for="higiene">Higiene Bucal</label>
                        <select name="higiene_bucal" id="higiene" required>
                            <?php 
                            $niveles = ["Excelente", "Buena", "Regular", "Mala"];
                            foreach($niveles as $nivel):
                                $sel = ($datos['higiene_bucal'] ?? '') == $nivel ? 'selected' : '';
                                echo "<option value='$nivel' $sel>$nivel</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="form-section grid-area-evolucion">
                    <legend>E. Evolución y Tratamiento</legend>
                    <div class="form_input">
                        <label for="cie10">Diagnóstico CIE-10</label>
                        <input type="text" name="diagnostico_cie" id="cie10" value="<?php echo htmlspecialchars($datos['diagnostico_cie10'] ?? ''); ?>" required>
                    </div>
                    <div class="form_input">
                        <label for="pronostico">Pronóstico</label>
                        <select name="pronostico" id="pronostico" required>
                            <option value="Favorable" <?php echo ($datos['pronostico'] ?? '') == 'Favorable' ? 'selected' : ''; ?>>Favorable</option>
                            <option value="Reservado" <?php echo ($datos['pronostico'] ?? '') == 'Reservado' ? 'selected' : ''; ?>>Reservado</option>
                            <option value="Desfavorable" <?php echo ($datos['pronostico'] ?? '') == 'Desfavorable' ? 'selected' : ''; ?>>Desfavorable</option>
                        </select>
                    </div>
                    <div class="form_input">
                        <label for="notas">Notas de Evolución</label>
                        <textarea name="notas_evolucion" id="notas" rows="4" required><?php echo htmlspecialchars($datos['notas_evolucion'] ?? ''); ?></textarea>
                    </div>
                </fieldset>
                
                <input type="submit" value="ACTUALIZAR DATOS" class="btn__enviar grid-area-enviar" onclick="return confirm('¿Está seguro que desea actualizar estos datos?')">
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
        function checkAlergia(input) {
            if (input.value.length > 2) {
                input.classList.add('alerta-roja');
            } else {
                input.classList.remove('alerta-roja');
            }
        }
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