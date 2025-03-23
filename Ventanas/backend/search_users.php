<?php
session_start();
include 'db_connection.php';

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$query = $_GET['query'] ?? '';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, nombre_usuario FROM Usuarios WHERE nombre_usuario LIKE ? AND id != ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => $conn->error]);
    exit();
}
$searchTerm = "%$query%";
$stmt->bind_param("si", $searchTerm, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

$stmt->close();
$conn->close();
?>