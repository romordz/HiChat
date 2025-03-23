<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los datos son JSON
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'application/json') !== false) {
        // Obtener el cuerpo de la solicitud en formato JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["status" => "error", "message" => "Datos JSON inválidos."]);
            exit();
        }
    } else {
        // Si no es JSON, asumir que es x-www-form-urlencoded
        $data = $_POST;
    }

    // Verificar si los datos necesarios están presentes
    if (!isset($data['chat_id']) || !isset($data['contenido'])) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos."]);
        exit();
    }

    $chat_id = $data['chat_id'];
    $usuario_id = $_SESSION['user_id'];
    $contenido = $data['contenido'];
    $fecha_envio = date('Y-m-d H:i:s');

    // Verificar si el chat existe
    $stmt = $conn->prepare("SELECT id FROM Chats WHERE id = ?");
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Chat no existe."]);
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    error_log("Mensaje recibido: " . print_r($data, true)); // Log de los datos recibidos

    $stmt = $conn->prepare("INSERT INTO Mensajes (chat_id, usuario_id, contenido, fecha_envio) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => $conn->error]);
        exit();
    }
    $stmt->bind_param("iiss", $chat_id, $usuario_id, $contenido, $fecha_envio);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>