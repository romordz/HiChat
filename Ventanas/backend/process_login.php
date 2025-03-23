<?php
session_start();
include 'db_connection.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, nombre_usuario, contraseña, foto_perfil, fecha_registro, marco_perfil FROM Usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombre_usuario, $hashed_password, $foto_perfil, $fecha_registro, $marco_perfil);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['nombre_usuario'] = $nombre_usuario;
            $_SESSION['email'] = $email;
            $_SESSION['foto_perfil'] = $foto_perfil;
            $_SESSION['fecha_registro'] = $fecha_registro;
            $_SESSION['marco_perfil'] = $marco_perfil;
            echo "Inicio de sesión exitoso. Redirigiendo al menú...";
            header("Location: ../php/menu.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "No se encontró una cuenta con ese correo electrónico.";
    }

    $stmt->close();
}

$conn->close();
?>