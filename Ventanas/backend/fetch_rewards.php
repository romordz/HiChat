<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id'];

// Obtener las recompensas y el progreso del usuario
$stmt = $conn->prepare("
    SELECT Recompensas.*, Usuarios.tareas_completadas, 
    (SELECT COUNT(*) FROM Recompensas_Reclamadas WHERE Recompensas_Reclamadas.usuario_id = ? AND Recompensas_Reclamadas.recompensa_id = Recompensas.id) AS reclamada
    FROM Recompensas
    JOIN Usuarios ON Usuarios.id = ?
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rewards = [];
while ($row = $result->fetch_assoc()) {
    $rewards[] = $row;
}

echo json_encode($rewards);

$stmt->close();
$conn->close();
?>