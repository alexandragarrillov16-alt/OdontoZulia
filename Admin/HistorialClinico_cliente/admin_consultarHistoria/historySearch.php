<?php
// 1. Lógica PHP para capturar el mensaje de la sesión
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | ODONTOZULIA</title>
    <link href="../../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="container py-4 text-center">
                <h1 class="mb-5">MI HISTORIAL CLÍNICO</h1>

                <div class="row py-4">
                    <div class="col">
                        <div class="table-wrapper">
                            <table class="table table-sm table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>N° De Historia</th>
                                        <th>Odontologo</th>
                                        <th>Código Paciente</th>
                                        <th>Nombre Completo</th>
                                        <th>Cédula</th>
                                        <th>Motivo Consulta</th>
                                        <th>Enf. Actual</th>
                                        <th>Enf. Sistémicas</th>
                                        <th>Alergias</th> 
                                        <th>Habitos</th>
                                        <th>Embarazo</th>
                                        <th>Meses</th>
                                        <th>Medicamentos</th>
                                        <th>Estado de Mucosa</th>
                                        <th>Higiene Bucal</th>
                                        <th>Diagnóstico (CIE-10)</th>
                                        <th>Pronostico</th>
                                        <th>Evolución</th>
                                    </tr>
                                </thead>
                                <tbody id="content">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <footer>
        <div>
        © 2025 OdontoZulia, S.A. Todos los derechos reservados.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", getData);

        function getData() {
            let content = document.getElementById("content");

            fetch("load.php", {
                method: "POST"
            })
            .then(response => response.json())
            .then(data => {
                content.innerHTML = data.data;
            })
            .catch(err => console.error("Error al cargar datos:", err));
        }
    </script>

    <!-- Bootstrap core JS -->
    <script src="../../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>