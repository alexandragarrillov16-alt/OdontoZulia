<?php
session_start();
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con = connection();

    // 1. Recolección de Datos
    $username   = trim($_POST['username']); 
    $passwordd  = $_POST['passwordd'];
    $nombre     = trim($_POST['nombre']);
    $apellido   = trim($_POST['apellido']);
    $cedula     = trim($_POST['cedula']);
    $email      = $_POST['email'];
    $telefono   = trim($_POST['telefono']);
    $fecha_nac  = $_POST['fecha_nacimiento'];
    $cargo      = $_POST['cargo'];

    // Lógica de Horario (nulo para Clientes)
    $horario_trabajo = null;

    // --- CONFIGURACIÓN DE REDIRECCIÓN ---
    // Si es registro público va a registro.php, si es admin va a admin_users.php
    $redirect_url = ($cargo === 'Cliente') ? "registro.php" : "admin_users.php";

    // -----------------------------------------------------------
    // VALIDACIÓN 1: FORMATO DE CÉDULA
    // -----------------------------------------------------------
    if (!preg_match('/^[VE]-[\d.]+$/', $cedula)) {
        $_SESSION['mensaje_error'] = "Formato de cédula incorrecto. Debe ser V- o E- seguido de números y puntos.";
        header("Location: $redirect_url");
        exit();
    }

    // -----------------------------------------------------------
    // VALIDACIÓN 2: MAYORÍA DE EDAD
    // -----------------------------------------------------------
    $nacimiento = new DateTime($fecha_nac);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;

    if ($edad < 18) {
        $_SESSION['mensaje_error'] = "Debes ser mayor de edad para registrarte en el sistema. (Edad actual: $edad años).";
        header("Location: $redirect_url");
        exit();
    }

    // -----------------------------------------------------------
    // VALIDACIÓN 3: COMPROBAR DUPLICADOS
    // -----------------------------------------------------------
    $check_sql = "SELECT username, cedula FROM users WHERE username = ? OR cedula = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $cedula);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if ($fila = mysqli_fetch_assoc($result)) {
            if ($fila['username'] === $username) {
                $_SESSION['mensaje_error'] = "El nombre de usuario '$username' ya está en uso. Por favor elige otro.";
            } else {
                $_SESSION['mensaje_error'] = "La cédula '$cedula' ya se encuentra registrada en nuestra base de datos.";
            }
            mysqli_stmt_close($check_stmt);
            mysqli_close($con);
            header("Location: $redirect_url");
            exit();
        }
        mysqli_stmt_close($check_stmt);
    }

    // -----------------------------------------------------------
    // PROCESO FINAL: INSERCIÓN
    // -----------------------------------------------------------
    // Usamos PASSWORD_DEFAULT para mayor seguridad
    $hashed_password = password_hash($passwordd, PASSWORD_DEFAULT);
    
    $sql_principal = "INSERT INTO users (
        username, passwordd, nombre, apellido, cedula, email, telefono, fecha_nacimiento, cargo, horario_trabajo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_principal = mysqli_prepare($con, $sql_principal);

    if ($stmt_principal) {
        mysqli_stmt_bind_param(
            $stmt_principal, 
            "ssssssssss", 
            $username, 
            $hashed_password, 
            $nombre, 
            $apellido, 
            $cedula, 
            $email, 
            $telefono, 
            $fecha_nac, 
            $cargo,
            $horario_trabajo
        );

        if (mysqli_stmt_execute($stmt_principal)) {
            $_SESSION['mensaje_exito'] = "Tu cuenta ha sido creada con éxito. Ahora puedes iniciar sesión con tu usuario '$username'.";
            // Si es cliente, lo mandamos al login para que entre de una vez
            $redirect_url = ($cargo === 'Cliente') ? "login.php" : "admin_users.php";
        } else {
            $_SESSION['mensaje_error'] = "Hubo un problema técnico al guardar tus datos. Inténtalo más tarde.";
        }
        mysqli_stmt_close($stmt_principal);
    }

    mysqli_close($con);
    header("Location: $redirect_url");
    exit();
}