<?php

require '../../../config.php';

// --- 1. Definición de Columnas de la tabla 'pacientes' ---

$columns_select = [
    'nro_historia', 
    'codigo_paciente', 
    'nombre', 
    'cedula', 
    'fecha_nacimiento', 
    'sexo', 
    'ocupacion', 
    'direccion', 
    'telefono', 
    'email'
];

// Nombre de la tabla y clave primaria
$table = "pacientes"; 
$id = 'codigo_paciente'; 


$campo = isset($_POST['campo']) ? $conn->real_escape_string($_POST['campo']) : null;

// --- 2. Filtrado (WHERE) ---
$where = '';

if ($campo != null) {
    $where = "WHERE (";
    $cont = count($columns_select); 
    for ($i = 0; $i < $cont; $i++) {
        $where .= $columns_select[$i] . " LIKE '%" . $campo . "%' OR ";
    }
    $where = substr_replace($where, "", -3); 
    $where .= ")";
}

// Limites para paginación
$limit = isset($_POST['registros']) ? $conn->real_escape_string($_POST['registros']) : 10;
$pagina = isset($_POST['pagina']) ? $conn->real_escape_string($_POST['pagina']) : 0;

if (!$pagina) {
    $inicio = 0;
    $pagina = 1;
} else {
    $inicio = ($pagina - 1) * $limit;
}

$sLimit = "LIMIT $inicio , $limit";

// Ordenamiento
$sOrder = "";
if (isset($_POST['orderCol'])) {
    $orderCol = $_POST['orderCol'];
    $oderType = isset($_POST['orderType']) ? $_POST['orderType'] : 'asc';
    $sOrder = "ORDER BY " . $columns_select[intval($orderCol)] . ' ' . $oderType;
}

// --- 3. Consulta Principal (SELECT) ---
// Eliminados los LEFT JOIN ya que todo está en una sola tabla
$sql = "SELECT " . implode(", ", $columns_select) . "
FROM $table
$where
$sOrder
$sLimit";

$resultado = $conn->query($sql);
$num_rows = $resultado->num_rows;

// --- 4. Consultas de Conteo (COUNT) ---

// Total registros con filtro
$sqlFiltro = "SELECT COUNT($id) AS num FROM $table $where";
$resFiltro = $conn->query($sqlFiltro);
$row_filtro = $resFiltro->fetch_array();
$totalFiltro = $row_filtro['num'];

// Total registros en la tabla sin filtro
$sqlTotal = "SELECT count($id) FROM $table";
$resTotal = $conn->query($sqlTotal);
$row_total = $resTotal->fetch_array();
$totalRegistros = $row_total[0];

// --- 5. Preparación de la Salida JSON ---
$output = [];
$output['totalRegistros'] = $totalRegistros;
$output['totalFiltro'] = $totalFiltro;
$output['data'] = '';
$output['paginacion'] = '';

$num_columnas_visibles = count($columns_select) + 1; // Columnas + botón acción

if ($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $output['data'] .= '<tr>';
        
        // Imprime las celdas de datos
        foreach ($columns_select as $col_name) {
            $output['data'] .= '<td>' . $row[$col_name] . '</td>';
        }
        
        $params = [];
        foreach ($columns_select as $col_name) {
            $params[] = $col_name . '=' . urlencode($row[$col_name]);
        }
        $url_history = "aggHistory.php?" . implode('&', $params);
        
        $output['data'] .= '<td><a class="btn btn-warning btn-sm" href="' . $url_history . '">Crear</a></td>';
        $output['data'] .= '</tr>';
    }
} else {
    $output['data'] .= '<tr><td colspan="' . $num_columnas_visibles . '">Sin resultados</td></tr>';
}

// Paginación (HTML para los botones)
if ($totalRegistros > 0) {
    $totalPaginas = ceil($totalFiltro / $limit);
    $output['paginacion'] .= '<nav><ul class="pagination">';
    $numeroInicio = max(1, $pagina - 4);
    $numeroFin = min($totalPaginas, $numeroInicio + 9);

    for ($i = $numeroInicio; $i <= $numeroFin; $i++) {
        $output['paginacion'] .= '<li class="page-item' . ($pagina == $i ? ' active' : '') . '">';
        $output['paginacion'] .= '<a class="page-link" href="#" onclick="nextPage(' . $i . ')">' . $i . '</a>';
        $output['paginacion'] .= '</li>';
    }
    $output['paginacion'] .= '</ul></nav>';
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);