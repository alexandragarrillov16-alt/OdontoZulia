<?php
session_start();
require '../../../config.php';

if (isset($_GET['id'])) {
    $id_usuario = $conn->real_escape_string($_GET['id']);

    $sql = "DELETE FROM users WHERE id = '$id_usuario'";

    if ($conn->query($sql)) {
        $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al intentar eliminar el usuario.";
    }
}

// Redirigir de vuelta a la lista de usuarios
header("Location: admin_userSearch.php"); 
exit();