<?php
session_start();
require '../../../connection.php';
$con = connection();

$nro_historia = $_POST['nro_historia']; 

$codigo_paciente = $_POST['codigo_paciente']; 
$nombre = $_POST['nombre']; 
$cedula = $_POST['cedula']; 
$motivo_consulta = $_POST['motivo_consulta'];
$enfermedad_actual = $_POST['enfermedad_actual'];

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

// 2. Preparar la consulta SQL de ACTUALIZACIÓN (UPDATE)
$sql = "UPDATE historia_pacientes SET 
            motivo_consulta = ?, 
            enfermedad_actual = ?, 
            enfermedades_sistemicas = ?, 
            alergias = ?, 
            habitos = ?, 
            embarazo = ?, 
            meses_embarazo = ?, 
            medicamentos = ?, 
            estado_mucosa = ?, 
            higiene_bucal = ?, 
            diagnostico_cie10 = ?, 
            pronostico = ?, 
            notas_evolucion = ?,
            fecha_actualizacion = NOW()
        WHERE nro_historia = ?";

if ($stmt = $con->prepare($sql)) {
    $stmt->bind_param("ssssssissssssi", 
        $motivo_consulta, 
        $enfermedad_actual, 
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
        $notas_evolucion,
        $nro_historia
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Los datos de la historia clínica han sido actualizados correctamente. ✅";
    } else {
        $_SESSION['mensaje_error'] = "Error al actualizar los datos: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['mensaje_error'] = "Error en la preparación de la consulta: " . $con->error;
}

$con->close();

header("Location: historySearch.php");
exit();