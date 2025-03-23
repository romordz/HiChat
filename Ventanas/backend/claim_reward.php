<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reward_id = $_POST['reward_id'];
    $user_id = $_SESSION['user_id'];

    // Verificar que el usuario tiene suficientes tareas completadas para reclamar la recompensa
    $stmt = $conn->prepare("
        SELECT tareas_completadas, tareas_requeridas, nombre
        FROM Usuarios, Recompensas
        WHERE Usuarios.id = ? AND Recompensas.id = ?
    ");
    $stmt->bind_param("ii", $user_id, $reward_id);
    $stmt->execute();
    $stmt->bind_result($tareas_completadas, $tareas_requeridas, $nombre_recompensa);
    $stmt->fetch();
    $stmt->close();

    if ($tareas_completadas < $tareas_requeridas) {
        echo json_encode(["status" => "error", "message" => "No tienes suficientes tareas completadas para reclamar esta recompensa."]);
        exit();
    }

    // Aplicar el marco de perfil al usuario
    $stmt = $conn->prepare("UPDATE Usuarios SET marco_perfil = ? WHERE id = ?");
    $stmt->bind_param("si", $nombre_recompensa, $user_id);
    if ($stmt->execute()) {
        // Guardar la recompensa reclamada en la tabla Recompensas_Reclamadas
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO Recompensas_Reclamadas (usuario_id, recompensa_id, fecha_reclamada) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $reward_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
}
?>