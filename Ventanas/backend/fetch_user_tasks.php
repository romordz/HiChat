<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT Tareas.*, Grupos.nombre AS grupo_nombre, Usuarios.nombre_usuario AS creador_nombre
    FROM Tareas
    JOIN Grupos ON Tareas.grupo_id = Grupos.id
    JOIN Usuarios_Grupos ON Grupos.id = Usuarios_Grupos.grupo_id
    JOIN Usuarios ON Tareas.usuario_id = Usuarios.id
    WHERE Usuarios_Grupos.usuario_id = ?
    ORDER BY Tareas.fecha_limite ASC
");
$stmt->bind_param("i", $user_id);
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