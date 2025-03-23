<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marco_perfil = $_POST['marco_perfil'];
    $user_id = $_SESSION['user_id'];

    // Actualizar el marco de perfil del usuario
    $stmt = $conn->prepare("UPDATE Usuarios SET marco_perfil = ? WHERE id = ?");
    $stmt->bind_param("si", $marco_perfil, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
    $conn->close();
}
?>