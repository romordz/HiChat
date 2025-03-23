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
    SELECT Tareas.*, Usuarios.nombre_usuario AS creador_nombre
    FROM Tareas
    JOIN Usuarios ON Tareas.usuario_id = Usuarios.id
    WHERE Tareas.grupo_id = ?
    ORDER BY Tareas.fecha_limite ASC
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode($tasks);

$stmt->close();
$conn->close();
?>