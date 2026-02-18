<?php 
    session_start();
    
    // Conexión a la base de datos
    include('../../../connection.php');
    $con = connection();

    // 1. Recibir y limpiar los datos del formulario
    $codigo_paciente = $_POST['codigo_paciente']; 
    $nro_historia    = $_POST['nro_historia'];
    $nombre          = $_POST['nombre'];
    $cedula          = $_POST['cedula'];
    $fecha_nac       = $_POST['fecha_nacimiento'];
    $sexo            = $_POST['sexo'];
    $ocupacion       = $_POST['ocupacion'];
    $direccion       = $_POST['direccion'];
    $telefono        = $_POST['telefono'];
    $email           = $_POST['email'];

    // Iniciar Transacción por seguridad
    mysqli_begin_transaction($con);
    $success = true;

    // ----------------------------------------------------------------------------------
    // --- ACTUALIZACIÓN DEL PACIENTE ---
    // ----------------------------------------------------------------------------------
    $sql_update = "UPDATE pacientes 
                    SET nombre = ?, 
                        cedula = ?, 
                        fecha_nacimiento = ?, 
                        sexo = ?, 
                        ocupacion = ?, 
                        direccion = ?, 
                        telefono = ?, 
                        email = ?,
                        nro_historia = ?
                    WHERE codigo_paciente = ?";
    
    if ($stmt = mysqli_prepare($con, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "ssssssssss", 
            $nombre, $cedula, $fecha_nac, $sexo, $ocupacion, 
            $direccion, $telefono, $email, $nro_historia, $codigo_paciente);

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
        $_SESSION['mensaje_exito'] = "Los datos del paciente **" . $nombre . "** (ID: " . $codigo_paciente . ") han sido actualizados. ✅";
    } else {
        mysqli_rollback($con);
        $_SESSION['mensaje_error'] = "❌ Error al actualizar al paciente. Detalle: " . $error_msg;
    }

    mysqli_close($con);
    
    // Redireccionamos a la búsqueda o lista de pacientes
    header("location: search.php");
    exit();
?>