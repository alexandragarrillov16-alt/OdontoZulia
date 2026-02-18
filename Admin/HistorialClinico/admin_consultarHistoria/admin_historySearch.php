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

        <div class="container py-4 text-center">
            <h1>HISTORIAL CLÍNICO ELETRÓNICO</h1>

            <div class="row g-4 d-flex justify-content-between align-items-center">

                <div class="col-auto d-flex align-items-center">
                    <label for="num_registros" class="col-form-label me-2">Mostrar: </label>

                    <select name="num_registros" id="num_registros" class="form-select w-auto me-2">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    
                    <label for="num_registros" class="col-form-label">registros</label>
                </div>
                
                <div class="col-auto d-flex align-items-center">
                    <label for="campo" class="col-form-label me-2">Buscar: </label>
                    
                    <input type="text" name="campo" id="campo" class="form-control w-auto">
                </div>

            </div>

            <div class="row py-4">
                <div class="col">
                    <div class="table-wrapper">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                                    <th class="sort asc">N° De Historia</th>
                                    <th class="sort asc">Código Paciente</th>
                                    <th class="sort asc">Nombre Completo</th>
                                    <th class="sort asc">Cédula</th>
                                    <th class="sort asc">Motivo Consulta</th>
                                    <th class="sort asc">Enf. Actual</th>
                                    <th class="sort asc">Enf. Sistémicas</th>
                                    <th class="sort asc">Alergias</th> 
                                    <th class="sort asc">Habitos</th>
                                    <th class="sort asc">Embarazo</th>
                                    <th class="sort asc">Meses De Embarazo</th>
                                    <th class="sort asc">Medicamentos</th>
                                    <th class="sort asc">Higiene Bucal</th>
                                    <th class="sort asc">Diagnóstico (CIE-10)</th>
                                    <th class="sort asc">Pronostico</th>
                                    <th class="sort asc">Evolución y tratamiento</th>
                                    <th class="sort asc">Última Actualización</th>
                                    <th></th>
                            </thead>

                            <!-- El id del cuerpo de la tabla. -->
                            <tbody id="content">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row justify-content-between">

                <div class="col-12 col-md-4">
                    <label id="lbl-total"></label>
                </div>

                <div class="col-12 col-md-4" id="nav-paginacion"></div>

                <input type="hidden" id="pagina" value="1">
                <input type="hidden" id="orderCol" value="0">
                <input type="hidden" id="orderType" value="asc">

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
        // Llamando a la función getData() al cargar la página
        document.addEventListener("DOMContentLoaded", getData);

        // Función para obtener datos con AJAX
        function getData() {
            let input = document.getElementById("campo").value
            let num_registros = document.getElementById("num_registros").value
            let content = document.getElementById("content")
            let pagina = document.getElementById("pagina").value || 1;
            let orderCol = document.getElementById("orderCol").value
            let orderType = document.getElementById("orderType").value

            let formaData = new FormData()
            formaData.append('campo', input)
            formaData.append('registros', num_registros)
            formaData.append('pagina', pagina)
            formaData.append('orderCol', orderCol)
            formaData.append('orderType', orderType)

            fetch("load.php", {
                    method: "POST",
                    body: formaData
                })
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = data.data
                    document.getElementById("lbl-total").innerHTML = `Mostrando ${data.totalFiltro} de ${data.totalRegistros} registros`;
                    document.getElementById("nav-paginacion").innerHTML = data.paginacion

                    // Si la página actual no tiene resultados, ajustar la paginación para mostrar la primera página
                    if (data.data.includes('Sin resultados') && parseInt(pagina) !== 1) {
                        nextPage(1); // Ir a la primera página
                    }
                })
                .catch(err => console.log(err))
        }

        // Función para cambiar de página
        function nextPage(pagina) {
            document.getElementById('pagina').value = pagina
            getData()
        }

        // Función para ordenar columnas
        function ordenar(e) {
            let elemento = e.target;
            let orderType = elemento.classList.contains("asc") ? "desc" : "asc";

            document.getElementById('orderCol').value = elemento.cellIndex;
            document.getElementById("orderType").value = orderType;
            elemento.classList.toggle("asc");
            elemento.classList.toggle("desc");

            getData()
        }
        // Función para cambiar de página
        function resetPagina() {
            document.getElementById('pagina').value = 1
            getData()
        }
        // Event listeners para los eventos de cambio en el campo de entrada y el select
        document.getElementById("campo").addEventListener("keyup", resetPagina);
        document.getElementById("num_registros").addEventListener("change", getData);

        // Event listener para ordenar las columnas
        let columns = document.querySelectorAll(".sort");
        columns.forEach(column => {
            column.addEventListener("click", ordenar);
        });

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

    <!-- Bootstrap core JS -->
    <script src="../../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>