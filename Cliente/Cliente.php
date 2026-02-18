<?php
// 1. Iniciar la sesión
session_start();

// 2. Se define la URL de redirección si falla la autenticación 
$login_url = "../login/login.php"; 

// 3. Se verifica la autenticación

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_url);
    exit(); 
}

if ($_SESSION['cargo'] !== 'Cliente') {
    header("Location: ../Login/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLIENTE | ODONTOZULIA</title>
    <link rel="stylesheet" href="styles_admin.css">
    <link rel="icon" type="image/png" href="../Img/logoo.png">
</head>
<body>
    <header class="header">
        <div class="header__logo-container">
            <img src="../Img/LogoCompleto.png" alt="logo" class="header__logo">
        </div>
        <div class="user-dropdown-container">
        <input type="checkbox" id="open__button">
        <label for="open__button" class="header__open-nav-button" role="button"> 
            <img src="../Img/usuario.png" class="header__user-logo">
            <?php 
                $nombreUsuario = $_SESSION['username'] ?? 'USUARIO';
                echo htmlspecialchars(strtoupper($nombreUsuario)); 
            ?>
        </label>
        <nav class="header__nav">
            <ul class="header__nav-list">
                <li class="header__nav-item"><a href="logout.php">Salir</a></li>
            </ul>
        </nav>
        </div>
    </header>
    <div class="grid">
    <nav class="nav__search">
        <ul>
            <details>
                <summary>
                    <img src="../Img/citas.png" alt="" class="menu-icon">
                    Agenda De Citas
                </summary>
                <li><a href="/Cliente/Citas/agendar/cita.php">Agendar Cita</a></li>
                <li><a href="/Cliente/Citas/consulta/consultar.php">Consultar Cita</a></li>
            </details>
            <details>
                <summary>
                    <img src="../Img/historia.png" alt="" class="menu-icon">
                    Historia Clínica Electrónica
                </summary>
                <li><a href="/Cliente/HistorialClinico/consultarHistoria/historySearch.php">Mi Historial Clínico</a></li>
            </details>
        </ul>
    </nav>
    <main>
        <div class="fondo"></div>
    </main>
    </div>
    <footer>
        <div>
        © 2025 OdontoZulia, S.A. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>