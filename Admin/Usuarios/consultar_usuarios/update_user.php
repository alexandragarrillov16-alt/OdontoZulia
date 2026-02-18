<?php 
    session_start();
    
    // Conexión a la base de datos
    include('../../../connection.php');
    $con = connection();

    // 1. Recibir y limpiar los datos del formulario
    $id_usuario     = $_POST['id']; 
    $username       = trim($_POST['username']);
    $password_nueva = $_POST['passwordd']; // Puede venir vacío
    $nombre         = trim($_POST['nombre']);
    $apellido       = trim($_POST['apellido']);
    $cedula         = trim($_POST['cedula']);
    $email          = $_POST['email'];
    $telefono       = $_POST['telefono'];
    $fecha_nac      = $_POST['fecha_nacimiento'];
    $cargo          = $_POST['cargo'];

    // -----------------------------------------------------------
    // VALIDACIÓN: FORMATO DE CÉDULA
    // -----------------------------------------------------------
    if (!preg_match('/^[VE]-[\d.]+$/', $cedula)) {
        $_SESSION['mensaje_error'] = "❌ Formato de cédula incorrecto. Debe ser V- o E- seguido de números y puntos.";
        header("Location: admin_userSearch.php");
        exit();
    }

    // -----------------------------------------------------------
    // VALIDACIÓN: MAYORÍA DE EDAD
    // -----------------------------------------------------------
    $nacimiento = new DateTime($fecha_nac);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;

    if ($edad < 18) {
        $_SESSION['mensaje_error'] = "❌ Error: El usuario debe ser mayor de edad ($edad años).";
        header("Location: admin_userSearch.php");
        exit();
    }

    mysqli_begin_transaction($con);
    $success = true;

    // ----------------------------------------------------------------------------------
    // --- ACTUALIZACIÓN (CON O SIN CONTRASEÑA) ---
    // ----------------------------------------------------------------------------------
    if (!empty($password_nueva)) {
        // Encriptamos la nueva clave si se escribió algo
        $hashed_password = password_hash($password_nueva, PASSWORD_DEFAULT);
        
        $sql_update = "UPDATE users 
                        SET username = ?, passwordd = ?, nombre = ?, apellido = ?, 
                            cedula = ?, email = ?, telefono = ?, fecha_nacimiento = ?, 
                            cargo = ?
                        WHERE id = ?";
        
        $stmt = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt, "sssssssssi", 
            $username, $hashed_password, $nombre, $apellido, 
            $cedula, $email, $telefono, $fecha_nac, $cargo, $id_usuario);
    } else {
        // Actualización sin tocar la contraseña
        $sql_update = "UPDATE users 
                        SET username = ?, nombre = ?, apellido = ?, 
                            cedula = ?, email = ?, telefono = ?, fecha_nacimiento = ?, 
                            cargo = ?
                        WHERE id = ?";
        
        $stmt = mysqli_prepare($con, $sql_update);
        mysqli_stmt_bind_param($stmt, "ssssssssi", 
            $username, $nombre, $apellido, 
            $cedula, $email, $telefono, $fecha_nac, $cargo, $id_usuario);
    }

    // Ejecución de la consulta
    if ($stmt) {
        if (!mysqli_stmt_execute($stmt)) {
            $success = false;
            $error_msg = mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $success = false;
        $error_msg = mysqli_error($con);
    }

    // ----------------------------------------------------------------------------------
    // --- FINALIZAR OPERACIÓN ---
    // ----------------------------------------------------------------------------------
    if ($success) {
        mysqli_commit($con);
        $_SESSION['mensaje_exito'] = "Los datos del usuario **" . $username . "** han sido actualizados. ✅";
    } else {
        mysqli_rollback($con);
        $_SESSION['mensaje_error'] = "❌ Error al actualizar: " . $error_msg;
    }

    mysqli_close($con);
    header("location: admin_userSearch.php");
    exit();
?>