<?php
session_start();
include('../connection.php');
$con = connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula  = trim($_POST['cedula']);
    $email   = trim($_POST['email']);
    $new_pass = $_POST['nueva_password'];

    $sql_check = "SELECT username FROM users WHERE cedula = ? AND email = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql_check);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $cedula, $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($res)) {

            $nuevo_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            
            $sql_update = "UPDATE users SET passwordd = ? WHERE cedula = ? AND email = ?";
            $stmt_up = mysqli_prepare($con, $sql_update);
            mysqli_stmt_bind_param($stmt_up, "sss", $nuevo_hash, $cedula, $email);
            
            if (mysqli_stmt_execute($stmt_up)) {
                $_SESSION['mensaje_exito'] = "¡Éxito! La contraseña del usuario<b>" . $row['username'] . "</b> ha sido actualizada.";
            } else {
                $_SESSION['mensaje_error'] = "Error interno al actualizar la base de datos.";
            }
            mysqli_stmt_close($stmt_up);
        } else {
            $_SESSION['mensaje_error'] = "Los datos no coinciden con nuestros registros.";
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($con);
    header("Location: recuperar.php");
    exit();
}