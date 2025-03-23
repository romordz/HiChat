<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$grupo_id = $_GET['grupo_id'] ?? null;
if (!$grupo_id) {
    echo json_encode(["status" => "error", "message" => "grupo_id no especificado."]);
    exit();
}

$stmt = $conn->prepare("
    SELECT Mensajes.*, Usuarios.nombre_usuario 
    FROM Mensajes 
    JOIN Usuarios ON Mensajes.usuario_id = Usuarios.id 
    WHERE grupo_id = ? 
    ORDER BY fecha_envio ASC
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
error_log("Mensajes recuperados: " . print_r($messages, true)); // Log de los mensajes recuperados
echo json_encode($messages);

$stmt->close();
$conn->close();
?>