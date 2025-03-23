<?php
include 'db_connection.php';

$error_message = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $contraseña = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (empty($nombre_usuario)) {
        $errors['username'] = "Por favor, ingresa un nombre de usuario.";
    }

    if (empty($email)) {
        $errors['email'] = "Por favor, ingresa un correo electrónico.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Por favor, ingresa un correo electrónico válido.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM Usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['email'] = "Este correo electrónico ya está en uso.";
        }
        $stmt->close();
    }

    if (empty($contraseña)) {
        $errors['password'] = "Por favor, ingresa una contraseña.";
    } elseif (strlen($contraseña) < 8) {
        $errors['password'] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!preg_match("/[A-Z]/", $contraseña) || !preg_match("/[a-z]/", $contraseña) || !preg_match("/[0-9]/", $contraseña)) {
        $errors['password'] = "La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número.";
    }

    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = "Por favor, confirma tu contraseña.";
    } elseif ($contraseña !== $confirmPassword) {
        $errors['confirmPassword'] = "Las contraseñas no coinciden.";
    }

    if (empty($errors)) {
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
        $foto_perfil = 'default.png';

        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
            $imageData = file_get_contents($_FILES['profilePicture']['tmp_name']);
            $foto_perfil = base64_encode($imageData);
        }

        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, email, contraseña, fecha_registro, foto_perfil) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->bind_param("ssss", $nombre_usuario, $email, $contraseña_hash, $foto_perfil);

        if ($stmt->execute() === TRUE) {
            echo "Registro exitoso. Redirigiendo a inicio de sesión...";
            header("Location: ../php/inicioSesion.php");
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>