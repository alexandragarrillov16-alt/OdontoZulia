<?php
session_start();
include('../../../connection.php');
$con = connection();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 1. RECOLECCIÓN DE DATOS (Incluyendo Nombre y Cédula)
    // -----------------------------------------------------------------------
    $nombre_paciente = $_POST['nombre']          ?? null;
    $cedula_paciente = $_POST['cedula']          ?? null;
    $telefono_paciente = $_POST['telefono']      ?? null;
    $email_paciente = $_POST['email']            ?? null;
    $odontologo      = $_POST['odontologo']      ?? null;
    $fecha_cita      = $_POST['fecha_cita']      ?? null;
    $hora_cita       = $_POST['hora_cita']       ?? null;
    $motivo          = $_POST['motivo']          ?? 'Consulta General';

    if (!$odontologo || !$fecha_cita || !$hora_cita) {
        throw new Exception("Faltan datos obligatorios para agendar la cita.");
    }

    // 2. VALIDACIÓN DE DISPONIBILIDAD
    // -----------------------------------------------------------------------
    $sql_check = "SELECT id_cita FROM citas WHERE fecha_cita = ? AND hora_cita = ? AND odontologo = ? AND estatus = 'PENDIENTE'";
    $stmt_check = mysqli_prepare($con, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "sss", $fecha_cita, $hora_cita, $odontologo);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        mysqli_stmt_close($stmt_check);
        throw new Exception("⚠️ El Dr. $odontologo ya tiene una cita ocupada en ese horario.");
    }
    mysqli_stmt_close($stmt_check);


    // 2.5 VALIDACIÓN: Solo una cita por cédula
    // -----------------------------------------------------------------------
    $sql_user_check = "SELECT id_cita FROM citas WHERE cedula = ? AND estatus = 'PENDIENTE'";
    $stmt_user = mysqli_prepare($con, $sql_user_check);
    mysqli_stmt_bind_param($stmt_user, "s", $cedula_paciente);
    mysqli_stmt_execute($stmt_user);
    mysqli_stmt_store_result($stmt_user);

    if (mysqli_stmt_num_rows($stmt_user) > 0) {
        mysqli_stmt_close($stmt_user);
        throw new Exception("El paciente ya tiene una cita pendiente en el sistema.");
    }
    mysqli_stmt_close($stmt_user);


    // 3. INSERCIÓN DE LA CITA
    // -----------------------------------------------------------------------
    mysqli_begin_transaction($con);

    $sql_insert = "INSERT INTO citas (nombre, cedula, telefono, email, fecha_cita, hora_cita, odontologo, motivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    
    if (!$stmt_insert) {
        throw new Exception("Error al preparar la inserción: " . mysqli_error($con));
    }

    // "sssssss" representa 7 strings
    mysqli_stmt_bind_param($stmt_insert, "ssssssss", 
        $nombre_paciente, 
        $cedula_paciente, 
        $telefono_paciente, 
        $email_paciente, 
        $fecha_cita, 
        $hora_cita, 
        $odontologo, 
        $motivo
    );
    
    mysqli_stmt_execute($stmt_insert);
    mysqli_stmt_close($stmt_insert);

    mysqli_commit($con);

    // 4. ÉXITO
    // -----------------------------------------------------------------------
    $_SESSION['mensaje_exito'] = "✅ Cita agendada para $nombre_paciente (CI: $cedula_paciente) con el Dr. $odontologo.";
    header("Location: searchcita.php"); 
    exit();

} catch (Exception $e) {
    if (isset($con) && $con->connect_errno == 0) { mysqli_rollback($con); }
    $_SESSION['mensaje_error'] = "❌ " . $e->getMessage();
    header("Location: searchcita.php");
    exit();
} finally {
    if (isset($con)) { mysqli_close($con); }
}