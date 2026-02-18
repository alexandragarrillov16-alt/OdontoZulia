<?php
session_start();
require '../../../config.php';

$user_id = $_SESSION['user_id'] ?? null;
$cedula_paciente = null;

if ($user_id !== null) {
    $sql_user = "SELECT cedula FROM users WHERE id = '$user_id' LIMIT 1";
    $res_user = $conn->query($sql_user);
    
    if ($res_user && $row_u = $res_user->fetch_assoc()) {
        $cedula_paciente = $row_u['cedula'];
    }
}

// --- Definición de Columnas
$columns_select = [
    'nro_historia', 
    'odontologo',
    'codigo_paciente', 
    'nombre', 
    'cedula', 
    'motivo_consulta', 
    'enfermedad_actual', 
    'enfermedades_sistemicas', 
    'alergias', 
    'habitos', 
    'embarazo', 
    'meses_embarazo', 
    'estado_mucosa',
    'higiene_bucal', 
    'diagnostico_cie10', 
];

$table = "historia_pacientes";
$output = ['data' => ''];

// --- Validación y Consulta de Historia ---
if ($cedula_paciente == null) {
    $output['data'] = '<tr><td colspan="19" class="text-center">No se pudo identificar la cédula del usuario conectado.</td></tr>';
} else {

    $cedula_escrita = $conn->real_escape_string($cedula_paciente);
    $sql = "SELECT " . implode(", ", $columns_select) . " FROM $table WHERE cedula = '$cedula_escrita' LIMIT 1";
    
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $output['data'] .= '<tr>';
            foreach ($columns_select as $col_name) {
                $style = "";

                if ($col_name == 'alergias' && !empty($row[$col_name]) && strtolower($row[$col_name]) != 'ninguna') {
                    $style = ' style="color: red; font-weight: bold;"';
                }
                $output['data'] .= '<td' . $style . '>' . htmlspecialchars($row[$col_name]) . '</td>';
            }
            $output['data'] .= '</tr>';
        }
    } else {
        $output['data'] = '<tr><td colspan="19" class="text-center">No existe un historial clínico registrado para la cédula: ' . htmlspecialchars($cedula_paciente) . '</td></tr>';
    }
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);