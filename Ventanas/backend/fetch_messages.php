<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$chat_id = $_GET['chat_id'] ?? null;
if (!$chat_id) {
    echo json_encode(["status" => "error", "message" => "chat_id no especificado."]);
    exit();
}

$stmt = $conn->prepare("SELECT Mensajes.*, Usuarios.nombre_usuario FROM Mensajes JOIN Usuarios ON Mensajes.usuario_id = Usuarios.id WHERE chat_id = ? ORDER BY fecha_envio ASC");
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($messages);
?>