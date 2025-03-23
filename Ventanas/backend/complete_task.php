<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tarea_id = $_POST['tarea_id'];
    $usuario_id = $_SESSION['user_id'];

    // Verificar que la tarea no fue creada por el mismo usuario
    $stmt = $conn->prepare("SELECT usuario_id FROM Tareas WHERE id = ?");
    $stmt->bind_param("i", $tarea_id);
    $stmt->execute();
    $stmt->bind_result($creador_id);
    $stmt->fetch();
    $stmt->close();

    if ($creador_id == $usuario_id) {
        echo json_encode(["status" => "error", "message" => "No puedes completar tu propia tarea."]);
        exit();
    }

    // Marcar la tarea como completada
    $stmt = $conn->prepare("UPDATE Tareas SET completada = 1 WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $tarea_id);

    if ($stmt->execute()) {
        // Incrementar el contador de tareas completadas
        $stmt->close(); // Cerrar el statement antes de crear uno nuevo
        $stmt = $conn->prepare("UPDATE Usuarios SET tareas_completadas = tareas_completadas + 1 WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $conn->close();
}
?>