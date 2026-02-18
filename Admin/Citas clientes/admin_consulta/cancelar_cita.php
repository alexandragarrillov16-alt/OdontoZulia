<?php
session_start();
require '../../../connection.php';
$con = connection();

if (isset($_GET['id'])) {
    $id_cita = $_GET['id'];

    $sql = "UPDATE citas SET estatus = 'CANCELADA' WHERE id_cita = ?";
    
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $id_cita);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje_exito'] = "Cita cancelada correctamente. ✕";
        } else {
            $_SESSION['mensaje_error'] = "Error al cancelar. ❌";
        }
        $stmt->close();
    }
}

header("Location: admin_consultar.php");
exit();