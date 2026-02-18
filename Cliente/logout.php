<?php
// 1. Inicia la sesión. Esto es crucial para poder acceder a las variables de sesión.
session_start();

// 2. Destruye la sesión.

// Desconfigura todas las variables de sesión
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión
session_destroy();

// 3. Redirige al usuario a la página de login 
header("Location: ../Login/login.php");
exit;
?>