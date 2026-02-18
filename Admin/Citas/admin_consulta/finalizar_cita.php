<?php
session_start();
require '../../../connection.php';
$con = connection();

if (isset($_GET['id'])) {
    $id_cita = $_GET['id'];

    $sql = "UPDATE citas SET estatus = 'COMPLETADA' WHERE id_cita = ?";
    
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $id_cita);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje_exito'] = "Cita finalizada exitosamente. ✅";
        } else {
            $_SESSION['mensaje_error'] = "Error SQL: " . $stmt->error;
        }
        $stmt->close();
    }
}

header("Location: admin_consultar.php");
exit();