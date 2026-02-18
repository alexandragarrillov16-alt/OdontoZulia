<?php
session_start();
require '../../../connection.php';
$con = connection();

// 1. Recoger datos del formulario
$codigo_paciente = $_POST['codigo_paciente']; 
$nombre = $_POST['nombre']; 
$cedula = $_POST['cedula']; 
$motivo_consulta = $_POST['motivo_consulta'];
$enfermedad_actual = $_POST['enfermedad_actual'];
$odontologo = $_POST['odontologo']; 

$enfermedades = isset($_POST['enf']) ? implode(", ", $_POST['enf']) : "Ninguna";

$alergias = $_POST['alergias'];
$habitos = $_POST['habitos'];
$embarazo = $_POST['embarazo'];
$meses_embarazo = !empty($_POST['meses_embarazo']) ? $_POST['meses_embarazo'] : 0;
$medicamentos_actuales = $_POST['medicamentos_actuales'];
$estado_mucosa = $_POST['estado_mucosa'];
$higiene_bucal = $_POST['higiene_bucal'];
$diagnostico_cie = $_POST['diagnostico_cie'];
$pronostico = $_POST['pronostico'];
$notas_evolucion = $_POST['notas_evolucion'];

// 2. consulta SQL
$sql = "INSERT INTO historia_pacientes (
            codigo_paciente, nombre, cedula, motivo_consulta, enfermedad_actual, 
            odontologo, enfermedades_sistemicas, 
            alergias, habitos, embarazo, meses_embarazo, medicamentos, 
            estado_mucosa, higiene_bucal, diagnostico_cie10, pronostico, notas_evolucion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $con->prepare($sql)) {
    $stmt->bind_param("ssssssssssissssss", 
        $codigo_paciente, 
        $nombre, 
        $cedula, 
        $motivo_consulta, 
        $enfermedad_actual, 
        $odontologo, 
        $enfermedades,
        $alergias, 
        $habitos, 
        $embarazo, 
        $meses_embarazo, 
        $medicamentos_actuales,
        $estado_mucosa, 
        $higiene_bucal, 
        $diagnostico_cie, 
        $pronostico, 
        $notas_evolucion
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Nuevo registro de historia para el paciente **$codigo_paciente** guardado exitosamente. ✅";
    } else {
        $_SESSION['mensaje_error'] = "Error al guardar la historia: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['mensaje_error'] = "Error en la preparación de la consulta: " . $con->error;
}

$con->close();

header("Location: admin_fichaSearch.php");
exit();
?>