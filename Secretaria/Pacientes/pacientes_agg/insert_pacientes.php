<?php
session_start();
include('../../../connection.php');
$con = connection();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // ----------------------------------------------------------------------------------
    // 1. GENERACIÓN DEL CÓDIGO ALEATORIO ÚNICO
    // ----------------------------------------------------------------------------------
    // Genera un ID único basado en el tiempo actual y lo convierte en un hash corto aleatorio
    $codigo_paciente = "PAC-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    // ----------------------------------------------------------------------------------
    // 2. RECOLECCIÓN DE DATOS DEL FORMULARIO
    // ----------------------------------------------------------------------------------
    $nombre    = $_POST['nombre'];
    $cedula    = $_POST['cedula'];
    $fecha_nac = $_POST['fecha_nacimiento'];
    $sexo      = $_POST['sexo'];
    $ocupacion = $_POST['ocupacion'];
    $direccion = $_POST['direccion'];
    $telefono  = $_POST['telefono'];
    $email     = $_POST['email'];

    mysqli_begin_transaction($con);

    // ----------------------------------------------------------------------------------
    // 3. INSERCIÓN
    // ----------------------------------------------------------------------------------
    $sql = "INSERT INTO pacientes (
        codigo_paciente, nombre, cedula, fecha_nacimiento, sexo, ocupacion, direccion, telefono, email
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param(
        $stmt, 
        "sssssssss", 
        $codigo_paciente, $nombre, $cedula, $fecha_nac, $sexo, $ocupacion, $direccion, $telefono, $email
    );
    
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($con);
    $_SESSION['mensaje_exito'] = "Registro exitoso. Código asignado: **" . $codigo_paciente . "** ✅";

} catch (Exception $e) {
    mysqli_rollback($con);

    if ($e->getCode() == 1062) {
        $_SESSION['mensaje_error'] = "⚠️ Error: El código o la cédula ya existen en el sistema.";
    } else {
        $_SESSION['mensaje_error'] = "❌ Error: " . $e->getMessage();
    }
}

mysqli_close($con); 
header("Location: agg.php");
exit();
?>