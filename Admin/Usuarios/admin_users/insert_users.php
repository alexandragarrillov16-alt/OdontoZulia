<?php
session_start();
include('../../../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con = connection();

    // 1. Recolección de Datos Básicos
    $username   = trim($_POST['username']); 
    $passwordd  = $_POST['passwordd'];
    $nombre     = trim($_POST['nombre']);
    $apellido   = trim($_POST['apellido']);
    $cedula     = trim($_POST['cedula']);
    $email      = $_POST['email'];
    $telefono   = $_POST['telefono'];
    $fecha_nac  = $_POST['fecha_nacimiento'];
    $cargo      = $_POST['cargo'];

    // 2. Lógica de Horario (Nuevo)
    // Solo si el cargo es Odontologo
    $horario_trabajo = null;
    if ($cargo === 'Odontologo' && !empty($_POST['hora_inicio']) && !empty($_POST['hora_fin'])) {
        $horario_trabajo = $_POST['hora_inicio'] . " - " . $_POST['hora_fin'];
    }

    // -----------------------------------------------------------
    // VALIDACIÓN 1: FORMATO DE CÉDULA (V- o E-)
    // -----------------------------------------------------------
    if (!preg_match('/^[VE]-[\d.]+$/', $cedula)) {
        $_SESSION['mensaje_error'] = "❌ Formato de cédula incorrecto. Debe ser V- o E- seguido solo de números y puntos.";
        header("Location: admin_users.php");
        exit();
    }

    // -----------------------------------------------------------
    // VALIDACIÓN 2: MAYORÍA DE EDAD (18 AÑOS)
    // -----------------------------------------------------------
    $nacimiento = new DateTime($fecha_nac);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;

    if ($edad < 18) {
        $_SESSION['mensaje_error'] = "❌ Error: El usuario debe ser mayor de edad. Edad calculada: $edad años.";
        header("Location: admin_users.php");
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
                $_SESSION['mensaje_error'] = "❌ El nombre de usuario **$username** ya está en uso.";
            } else {
                $_SESSION['mensaje_error'] = "❌ La cédula **$cedula** ya está registrada.";
            }
            mysqli_stmt_close($check_stmt);
            mysqli_close($con);
            header("Location: admin_users.php");
            exit();
        }
        mysqli_stmt_close($check_stmt);
    }

    // -----------------------------------------------------------
    // PROCESO FINAL: INSERCIÓN (Se agrega horario_trabajo)
    // -----------------------------------------------------------
    $hashed_password = password_hash($passwordd, PASSWORD_DEFAULT);
    
    $sql_principal = "INSERT INTO users (
        username, passwordd, nombre, apellido, cedula, email, telefono, fecha_nacimiento, cargo, horario_trabajo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_principal = mysqli_prepare($con, $sql_principal);

    if ($stmt_principal) {
        // "ssssssssss" -> 10 strings
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
            $_SESSION['mensaje_exito'] = "Usuario **$username** creado exitosamente. ✅";
        } else {
            $_SESSION['mensaje_error'] = "❌ Error de base de datos: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt_principal);
    }

    mysqli_close($con);
    header("Location: admin_users.php");
    exit();
}