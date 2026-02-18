<?php

function connection(){
    $host = "localhost"; 
    $user = "root";
    $pass = "";

    $bd = "odontobd";

    // 1. Establecer la conexión
    $connect = mysqli_connect($host, $user, $pass);

    // 2. Seleccionar la base de datos
    mysqli_select_db($connect, $bd);
    
    mysqli_set_charset($connect, "utf8"); 

    return $connect;
};

?>