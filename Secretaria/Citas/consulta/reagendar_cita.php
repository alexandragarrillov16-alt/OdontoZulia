<?php
session_start();
require '../../../connection.php';
$con = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_cita = $con->real_escape_string($_POST['id_cita']);
    $nueva_fecha = $con->real_escape_string($_POST['nueva_fecha']);
    $nueva_hora = $con->real_escape_string($_POST['nueva_hora']);

    $sql = "UPDATE citas 
            SET fecha_cita = '$nueva_fecha', 
                hora_cita = '$nueva_hora', 
                estatus = 'PENDIENTE' 
            WHERE id_cita = '$id_cita'";

    if ($con->query($sql)) {
        $_SESSION['mensaje_exito'] = "Cita reagendada. El estatus ha cambiado a PENDIENTE.";
    } else {
        $_SESSION['mensaje_error'] = "Error al intentar actualizar la cita: " . $con->error;
    }
}

// Redirigir de vuelta a la tabla de administración
header("Location: consultar.php"); 
exit();