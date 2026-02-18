<?php
session_start();
include('../../../connection.php');
$con = connection();

$filename = "Reporte_Citas_" . date('Ymd_His') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Obtenemos el valor del filtro
$filtro_cita = $_GET['filtro_cita'] ?? 'todo';

// Seleccionamos los campos exactos de tu imagen
$query = "SELECT id_cita, nombre, cedula, telefono, email, odontologo, fecha_cita, hora_cita, motivo, estatus, fecha_registro FROM citas WHERE 1=1";

if ($filtro_cita === 'mes' && !empty($_GET['fecha_mes_cita'])) {
    $query .= " AND fecha_cita LIKE '" . mysqli_real_escape_string($con, $_GET['fecha_mes_cita']) . "%'";
}

$result = mysqli_query($con, $query);
?>

<meta charset="utf-8">
<table border="1">
    <tr style="background-color: #003366; color: white; font-weight: bold;">
        <th>ID</th>
        <th>Paciente</th>
        <th>Cédula</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Odontólogo</th>
        <th>Fecha Cita</th>
        <th>Hora</th>
        <th>Motivo</th>
        <th>Estatus</th>
        <th>Fecha Registro</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['id_cita']; ?></td>
        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
        <td><?php echo htmlspecialchars($row['cedula']); ?></td>
        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['odontologo']); ?></td>
        <td><?php echo date("d/m/Y", strtotime($row['fecha_cita'])); ?></td>
        <td><?php echo date("h:i A", strtotime($row['hora_cita'])); ?></td>
        <td><?php echo htmlspecialchars($row['motivo']); ?></td>
        <td><?php echo htmlspecialchars($row['estatus']); ?></td>
        <td><?php echo $row['fecha_registro']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>