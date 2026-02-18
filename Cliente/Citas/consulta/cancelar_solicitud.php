<?php
session_start();
require '../../../connection.php';
$con = connection();

if (isset($_GET['id'])) {
    $id_solicitud = $_GET['id'];

    $sql = "DELETE FROM citas_pendientes WHERE id_solicitud = ? AND estado_revision = 'ESPERA'";
    
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje_exito'] = "Solicitud eliminada correctamente. ✕";
        } else {
            $_SESSION['mensaje_error'] = "No se pudo eliminar: La solicitud ya fue procesada o no existe.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje_error'] = "Error interno en el servidor. ❌";
    }
}

mysqli_close($con);
header("Location: consultar.php");
exit();