<?php

session_start();

include('../../../connection.php');
$con = connection();

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

$sql_medicos = "SELECT nombre, apellido FROM users WHERE cargo = 'Odontologo'";
$res_medicos = mysqli_query($con, $sql_medicos); 

if (!$res_medicos) {
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_adminreport.css">
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
    <div class="division__select">
        <h1 id="h1">GENERACIÓN DE REPORTES</h1>
        <label for="tipoFormulario">SELECCIONE EL MÓDULO</label>
        <select id="tipoFormulario" onchange="mostrarFormulario()">
            <option value="" selected disabled hidden>-- Seleccione una opción --</option>
            <option value="reporte_historial">HISTORIAL CLÍNICO DE PACIENTES</option>
            <option value="reporte_citas">AGENDA DE CITAS</option>
        </select>
    </div>

    <div id="reporte_historial" class="formulario-oculto">
        <h2 id="h2">Seleccione un filtro para el Historial</h2>
        <form action="export_historial.php" method="GET" class="grid-form">
            
            <div class="form_input_radio">
                <input type="radio" name="filtro_activo" value="codigo" id="f_codigo" checked onclick="gestionarInputs('historial')">
                <label for="f_codigo">Por Código</label>
                <input type="text" name="codigo_paciente" id="input_codigo" placeholder="Ej: 001">
            </div>

            <div class="form_input_radio">
                <input type="radio" name="filtro_activo" value="cedula" id="f_cedula" onclick="gestionarInputs('historial')">
                <label for="f_cedula">Por Cédula</label>
                <input type="text" name="cedula" id="input_cedula" placeholder="V-00.000.000" disabled>
            </div>

            <div class="form_input_radio">
                <input type="radio" name="filtro_activo" value="odontologo" id="f_odonto" onclick="gestionarInputs('historial')">
                <label for="f_odonto">Por Odontólogo</label>
                <select name="odontologo" id="input_odonto" disabled>
                <option selected disabled hidden>-- Todos los doctores --</option>
                    <?php
                    mysqli_data_seek($res_medicos, 0); 
                    while($medico = mysqli_fetch_assoc($res_medicos)): 
                        $nombreCompleto = $medico['nombre'] . " " . $medico['apellido']; ?>
                        <option value="<?php echo $nombreCompleto; ?>"><?php echo $nombreCompleto; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form_input_radio">
                <input type="radio" name="filtro_activo" value="fecha" id="f_fecha" onclick="gestionarInputs('historial')">
                <label for="f_fecha">Por Mes/Año</label>
                <input type="month" name="fecha_mes" id="input_fecha" disabled>
            </div>

            <div class="btn-group">
                <button type="submit" name="tipo" value="filtrado" class="btn">DESCARGAR FILTRADO</button>
                <button type="submit" name="tipo" value="total" class="btn btn-total">DESCARGAR TODO EL HISTORIAL</button>
            </div>
                </form>
            </div>

            <div id="reporte_citas" class="formulario-oculto">
                <h2 id="h2">Filtros para Agenda de Citas</h2>
                <form action="export_citas.php" method="GET" class="grid-form">
                    <div class="form_input_radio">
                        <input type="radio" name="filtro_cita" value="mes" id="fc_mes" checked onclick="gestionarInputs('citas')">
                        <label for="fc_mes">Filtrar por Mes</label>
                        <input type="month" name="fecha_mes_cita" id="input_cita_mes">
                    </div>
                    <div class="form_input_radio">
                        <input type="radio" name="filtro_cita" value="todo" id="fc_todo" onclick="gestionarInputs('citas')">
                        <label for="fc_todo">Sin filtros (Descargar todo)</label>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn">GENERAR REPORTE</button>
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
        function gestionarInputs(seccion) {
    if (seccion === 'historial') {
        const filtros = ['codigo', 'cedula', 'odonto', 'fecha'];
        filtros.forEach(f => {
            const isChecked = document.getElementById('f_' + f).checked;
            document.getElementById('input_' + f).disabled = !isChecked;
        });
    } else if (seccion === 'citas') {
        const isMesChecked = document.getElementById('fc_mes').checked;
        document.getElementById('input_cita_mes').disabled = !isMesChecked;
    }
}
    
    function mostrarFormulario() {
        // 1. Obtiene el valor seleccionado del <select>
        const selector = document.getElementById('tipoFormulario');
        const idSeleccionado = selector.value;

        // 2. Obtiene todos los formularios que tienen la clase 'formulario-oculto'
        const todosLosFormularios = document.querySelectorAll('.formulario-oculto');

        // 3. Oculta todos los formularios (ponerlos en estado base)
        todosLosFormularios.forEach(form => {
            form.classList.remove('formulario-visible'); // Quitar la clase que lo hace visible
        });

        // 4. Muestra el formulario seleccionado (si no es la opción vacía)
        if (idSeleccionado) {
            const formularioAMostrar = document.getElementById(idSeleccionado);
            
            // Verificar que el ID corresponde a un formulario existente
            if (formularioAMostrar) {
                formularioAMostrar.classList.add('formulario-visible'); // 
            }
        }
    }
    document.addEventListener('DOMContentLoaded', mostrarFormulario);

function copiarValorTipoBien(idDelCampoOculto) {
    // 1. Obtener el elemento SELECT de arriba
    var selectTipoFormulario = document.getElementById('tipoFormulario');
    var indiceSeleccionado = selectTipoFormulario.selectedIndex;
    var opcionSeleccionada = selectTipoFormulario.options[indiceSeleccionado];
    var textoVisible = opcionSeleccionada.textContent; 

    // 2. Obtener el campo oculto usando el ID que se pasó como argumento
    // ¡Aquí está la clave! Usa el argumento en lugar del ID fijo.
    var inputOculto = document.getElementById(idDelCampoOculto);

    // 3. Asignar el texto visible
    if (inputOculto) {
        inputOculto.value = textoVisible;
    } else {
        console.error('Error: No se encontró el campo oculto con ID:', idDelCampoOculto);
        return false; // Evita el envío si hay un error crítico
    }

    // Permitir que el formulario se envíe
    return true; 
}
    </script>
</body>
</html>