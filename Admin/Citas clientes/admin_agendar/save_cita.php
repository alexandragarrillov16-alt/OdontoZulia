<?php
session_start();
include('../../../connection.php');
$con = connection();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 1. RECOLECCIÓN DE DATOS
    // -----------------------------------------------------------------------
    $nombre_paciente   = $_POST['nombre']           ?? null;
    $cedula_paciente   = $_POST['cedula']           ?? null;
    $telefono_paciente = $_POST['telefono']         ?? null;
    $email_paciente    = $_POST['email']            ?? null;
    $odontologo        = $_POST['odontologo']       ?? null;
    $fecha_cita        = $_POST['fecha_cita']       ?? null;
    $hora_cita         = $_POST['hora_cita']        ?? null;
    $motivo            = $_POST['motivo']           ?? 'Consulta General';

    if (!$nombre_paciente || !$cedula_paciente || !$odontologo || !$fecha_cita || !$hora_cita) {
        throw new Exception("Faltan datos obligatorios para procesar la solicitud.");
    }

    // 2. VALIDACIÓN: cita confirmada
    // -----------------------------------------------------------------------
    $sql_real_check = "SELECT id_cita FROM citas WHERE cedula = ? AND estatus = 'PENDIENTE'";
    $stmt_real = mysqli_prepare($con, $sql_real_check);
    mysqli_stmt_bind_param($stmt_real, "s", $cedula_paciente);
    mysqli_stmt_execute($stmt_real);
    mysqli_stmt_store_result($stmt_real);

    if (mysqli_stmt_num_rows($stmt_real) > 0) {
        mysqli_stmt_close($stmt_real);
        throw new Exception("Ya posees una cita confirmada en el sistema. No puedes solicitar otra.");
    }
    mysqli_stmt_close($stmt_real);

    // 3. VALIDACIÓN: solicitud en espera de validación
    // -----------------------------------------------------------------------
    $sql_pend_check = "SELECT id_solicitud FROM citas_pendientes WHERE cedula = ? AND estado_revision = 'ESPERA'";
    $stmt_pend = mysqli_prepare($con, $sql_pend_check);
    mysqli_stmt_bind_param($stmt_pend, "s", $cedula_paciente);
    mysqli_stmt_execute($stmt_pend);
    mysqli_stmt_store_result($stmt_pend);

    if (mysqli_stmt_num_rows($stmt_pend) > 0) {
        mysqli_stmt_close($stmt_pend);
        throw new Exception("Ya tienes una solicitud de cita enviada. Por favor, espera a que la secretaria la valide.");
    }
    mysqli_stmt_close($stmt_pend);

    // 4. INSERCIÓN EN LA TABLA DE SOLICITUDES (citas_pendientes)
    // -----------------------------------------------------------------------
    mysqli_begin_transaction($con);

    $sql_insert = "INSERT INTO citas_pendientes (nombre, cedula, telefono, email, fecha_cita, hora_cita, odontologo, motivo, estado_revision) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ESPERA')";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    
    if (!$stmt_insert) {
        throw new Exception("Error al procesar la solicitud en la base de datos.");
    }

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

    // 5. RESPUESTA DE ÉXITO
    // -----------------------------------------------------------------------
    $_SESSION['mensaje_exito'] = "📩 Solicitud enviada correctamente. Su cita para el " . date('d/m/Y', strtotime($fecha_cita)) . " está en espera de validación.";
    header("Location: ../admin_consulta/admin_consultar.php"); 
    exit();

} catch (Exception $e) {
    if (isset($con) && $con->connect_errno == 0) { mysqli_rollback($con); }
    
    $_SESSION['mensaje_error'] = "❌ " . $e->getMessage();
    header("Location: ../admin_consulta/admin_consultar.php");
    exit();
} finally {
    if (isset($con)) { mysqli_close($con); }
}