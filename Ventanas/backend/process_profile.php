<?php
session_start();
include 'db_connection.php';

$user_id = $_GET['user_id'] ?? $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT nombre_usuario, email, foto_perfil, fecha_registro, tareas_completadas, marco_perfil FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nombre_usuario, $email, $foto_perfil, $fecha_registro, $tareas_completadas, $marco_perfil);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM Chats 
    WHERE usuario1_id = ? OR usuario2_id = ?
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$stmt->bind_result($num_chats);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM Usuarios_Grupos 
    WHERE usuario_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($num_grupos);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $foto_perfil = $_FILES['foto_perfil']['tmp_name'] ? base64_encode(file_get_contents($_FILES['foto_perfil']['tmp_name'])) : $foto_perfil;

    $stmt = $conn->prepare("UPDATE Usuarios SET nombre_usuario = ?, email = ?, foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre_usuario, $email, $foto_perfil, $user_id);

    if ($stmt->execute()) {
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['nombre_usuario'] = $nombre_usuario;
            $_SESSION['email'] = $email;
            $_SESSION['foto_perfil'] = $foto_perfil;
            $_SESSION['marco_perfil'] = $marco_perfil;
        }
        header("Location: ../php/perfil.php?user_id=$user_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>