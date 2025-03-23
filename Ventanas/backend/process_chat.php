<?php
session_start();
include 'db_connection.php';

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    die("Usuario no especificado.");
}

$current_user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("El usuario especificado no existe.");
}
$stmt->close();

$stmt = $conn->prepare("SELECT id FROM Chats WHERE (usuario1_id = ? AND usuario2_id = ?) OR (usuario1_id = ? AND usuario2_id = ?)");
$stmt->bind_param("iiii", $current_user_id, $user_id, $user_id, $current_user_id);
$stmt->execute();
$stmt->bind_result($chat_id);
$stmt->fetch();
$stmt->close();

if (!$chat_id) {
    $stmt = $conn->prepare("INSERT INTO Chats (usuario1_id, usuario2_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $current_user_id, $user_id);
    if ($stmt->execute()) {
        $chat_id = $stmt->insert_id;
    } else {
        die("Error al crear el chat: " . $stmt->error);
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT nombre_usuario, foto_perfil, marco_perfil FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nombre_usuario, $foto_perfil, $marco_perfil);
$stmt->fetch();
$stmt->close();
?>