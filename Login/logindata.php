<?php
// Inicia la sesión
session_start();

// Incluye el archivo de conexión a la base de datos
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Obtener y Limpiar los Datos del Formulario
    $username_ingresado = trim($_POST['username']);
    $password_ingresada = $_POST['passwordd'];

    $con = connection();

    // 2. Consulta Preparada: OBTENEMOS passwordd, id y cargo
    // Se eliminó el campo 'admin' y se agregó 'cargo'
    $sql = "SELECT passwordd, id, cargo FROM users WHERE username = ?";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username_ingresado);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Verificar si se encontró un usuario
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $hash_almacenado = $user['passwordd'];

            // 3. VERIFICAR CONTRASEÑA ENCRIPTADA
            if (password_verify($password_ingresada, $hash_almacenado)) {
                
                // AUTENTICACIÓN EXITOSA
                
                // 4. Crear variables de sesión
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $username_ingresado;
                $_SESSION['cargo']    = $user['cargo']; 

                // 5. LÓGICA de Redirección basada en 'cargo'
                // Dependiendo del valor en la base de datos, redirige a su página
                switch ($user['cargo']) {
                    case 'Admin':
                        header("Location: ../admin/admin.php");
                        break;
                    case 'Odontologo':
                        header("Location: ../Odontologo/odontologo.php");
                        break;
                    case 'Secretaria':
                        header("Location: ../secretaria/secretaria.php");
                        break;
                    case 'Cliente':
                        header("Location: ../cliente/cliente.php");
                        break;
                    default:
                        // Si el cargo no coincide con ninguno de los anteriores
                        header("Location: ../index.php");
                        break;
                }
                exit();

            } else {
                // ❌ Contraseña incorrecta
                $_SESSION['login_error'] = "❌ Usuario o contraseña incorrectos.";
                header("Location: login.php");
                exit();
            }

        } else {
            // ❌ Usuario no encontrado
            $_SESSION['login_error'] = "❌ Usuario o contraseña incorrectos.";
            header("Location: login.php");
            exit();
        }

        mysqli_stmt_close($stmt);

    } else {
        // ❌ Error interno
        $_SESSION['login_error'] = "❌ Error interno del sistema.";
        header("Location: login.php");
        exit();
    }

    mysqli_close($con);

} else {
    header("Location: login.php");
    exit();
}
?>