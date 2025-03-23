<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id'];

// Obtener las recompensas reclamadas por el usuario
$stmt = $conn->prepare("
    SELECT Recompensas.nombre
    FROM Recompensas
    JOIN Recompensas_Reclamadas ON Recompensas.id = Recompensas_Reclamadas.recompensa_id
    WHERE Recompensas_Reclamadas.usuario_id = ?
");
$stmt->bind_param("i", $user_id);
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