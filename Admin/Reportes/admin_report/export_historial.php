<?php
session_start();
include('../../../connection.php');
$con = connection();

$filename = "Reporte_Historial_Completo_" . date('Ymd_His') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

$tipo = $_GET['tipo'] ?? 'filtrado';
$filtro_seleccionado = $_GET['filtro_activo'] ?? '';

$query = "SELECT * FROM historia_pacientes WHERE 1=1";

if ($tipo === 'filtrado') {
    switch ($filtro_seleccionado) {
        case 'codigo':
            if (!empty($_GET['codigo_paciente'])) {
                $query .= " AND codigo_paciente = '" . mysqli_real_escape_string($con, $_GET['codigo_paciente']) . "'";
            }
            break;
        case 'cedula':
            if (!empty($_GET['cedula'])) {
                $query .= " AND cedula LIKE '%" . mysqli_real_escape_string($con, $_GET['cedula']) . "%'";
            }
            break;
        case 'odontologo':
            if (!empty($_GET['odontologo'])) {
                $query .= " AND odontologo = '" . mysqli_real_escape_string($con, $_GET['odontologo']) . "'";
            }
            break;
        case 'fecha':
            if (!empty($_GET['fecha_mes'])) {
                $query .= " AND fecha_actualizacion LIKE '" . mysqli_real_escape_string($con, $_GET['fecha_mes']) . "%'";
            }
            break;
    }
}

$result = mysqli_query($con, $query);
?>

<meta charset="utf-8">
<table border="1">
    <tr style="background-color: #004080; color: white; font-weight: bold;">
        <th>Nro Historia</th>
        <th>Odontólogo</th>
        <th>Código Paciente</th>
        <th>Nombre</th>
        <th>Cédula</th>
        <th>Motivo Consulta</th>
        <th>Enfermedad Actual</th>
        <th>Enfermedades Sistémicas</th>
        <th>Alergias</th>
        <th>Hábitos</th>
        <th>Embarazo</th>
        <th>Meses Embarazo</th>
        <th>Medicamentos</th>
        <th>Estado Mucosa</th>
        <th>Higiene Bucal</th>
        <th>Diagnóstico CIE10</th>
        <th>Pronóstico</th>
        <th>Notas Evolución</th>
        <th>Fecha Actualización</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['nro_historia']); ?></td>
        <td><?php echo htmlspecialchars($row['odontologo']); ?></td>
        <td><?php echo htmlspecialchars($row['codigo_paciente']); ?></td>
        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
        <td><?php echo htmlspecialchars($row['cedula']); ?></td>
        <td><?php echo htmlspecialchars($row['motivo_consulta']); ?></td>
        <td><?php echo htmlspecialchars($row['enfermedad_actual']); ?></td>
        <td><?php echo htmlspecialchars($row['enfermedades_sistemicas']); ?></td>
        <td><?php echo htmlspecialchars($row['alergias']); ?></td>
        <td><?php echo htmlspecialchars($row['habitos']); ?></td>
        <td><?php echo htmlspecialchars($row['embarazo']); ?></td>
        <td><?php echo htmlspecialchars($row['meses_embarazo']); ?></td>
        <td><?php echo htmlspecialchars($row['medicamentos']); ?></td>
        <td><?php echo htmlspecialchars($row['estado_mucosa']); ?></td>
        <td><?php echo htmlspecialchars($row['higiene_bucal']); ?></td>
        <td><?php echo htmlspecialchars($row['diagnostico_cie10']); ?></td>
        <td><?php echo htmlspecialchars($row['pronostico']); ?></td>
        <td><?php echo htmlspecialchars($row['notas_evolucion']); ?></td>
        <td><?php echo $row['fecha_actualizacion']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>