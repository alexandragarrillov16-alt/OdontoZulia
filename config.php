<?php

// Creando una nueva conexión a la base de datos.
$conn = new mysqli("localhost", "root", "", "odontobd");

// Comprobando si hay un error de conexión.
if ($conn->connect_error) {
    echo 'Error de conexion ' . $conn->connect_error;
    exit;
}