<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_FILES['documento']) || !isset($_POST['chat_id'])) {
        echo json_encode(["status" => "error", "message" => "Faltan datos requeridos"]);
        exit();
    }

    $chat_id = $_POST['chat_id'];
    $file = $_FILES['documento'];
    
    $upload_dir = "../uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $file_url = 'uploads/' . $unique_filename;
        
        echo json_encode([
            "status" => "success",
            "file_url" => $file_url
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error al subir el archivo"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Método no permitido"
    ]);
}
?>