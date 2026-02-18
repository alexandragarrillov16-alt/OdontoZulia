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
    // Marcamos como RECHAZADA
    $sql = "UPDATE citas_pendientes SET estado_revision = 'RECHAZADA' WHERE id_solicitud = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_solicitud);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['mensaje_exito'] = "🚫 La solicitud ha sido rechazada.";
    } else {
        throw new Exception("No se pudo rechazar la solicitud.");
    }

} catch (Exception $e) {
    $_SESSION['mensaje_error'] = "❌ Error: " . $e->getMessage();
}

header("Location: validar_citas.php");
exit();