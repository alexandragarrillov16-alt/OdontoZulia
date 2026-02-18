<?php
session_start();
include('../../../connection.php');
$con = connection();

if (!isset($_GET['id'])) {
    header("Location: validar_citas.php");
    exit();
}

$id_solicitud = $_GET['id'];

try {
    mysqli_begin_transaction($con);

    // 1. Obtener los datos de la solicitud pendiente
    $sql_fetch = "SELECT * FROM citas_pendientes WHERE id_solicitud = ?";
    $stmt_fetch = mysqli_prepare($con, $sql_fetch);
    mysqli_stmt_bind_param($stmt_fetch, "i", $id_solicitud);
    mysqli_stmt_execute($stmt_fetch);
    $resultado = mysqli_stmt_get_result($stmt_fetch);
    $datos = mysqli_fetch_assoc($resultado);

    if (!$datos) {
        throw new Exception("La solicitud no existe.");
    }

    // 2. Insertar en la tabla oficial 'citas'
    $sql_insert = "INSERT INTO citas (nombre, cedula, fecha_cita, hora_cita, odontologo, motivo, estatus) 
                    VALUES (?, ?, ?, ?, ?, ?, 'PENDIENTE')";
    $stmt_insert = mysqli_prepare($con, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "ssssss", 
        $datos['nombre'], 
        $datos['cedula'], 
        $datos['fecha_cita'], 
        $datos['hora_cita'], 
        $datos['odontologo'], 
        $datos['motivo']
    );
    mysqli_stmt_execute($stmt_insert);

    // 3. Actualizar el estado en 'citas_pendientes' a VALIDADA
    // (Opcional: puedes usar DELETE si no quieres guardar historial de solicitudes)
    $sql_update = "UPDATE citas_pendientes SET estado_revision = 'VALIDADA' WHERE id_solicitud = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "i", $id_solicitud);
    mysqli_stmt_execute($stmt_update);

    mysqli_commit($con);
    $_SESSION['mensaje_exito'] = "✅ Cita validada y agendada correctamente.";

} catch (Exception $e) {
    mysqli_rollback($con);
    $_SESSION['mensaje_error'] = "❌ Error: " . $e->getMessage();
}

header("Location: validar_citas.php");
exit();