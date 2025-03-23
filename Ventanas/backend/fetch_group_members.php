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
    SELECT Usuarios.id, Usuarios.nombre_usuario, Usuarios.foto_perfil, Usuarios.marco_perfil
    FROM Usuarios 
    JOIN Usuarios_Grupos ON Usuarios.id = Usuarios_Grupos.usuario_id 
    WHERE Usuarios_Grupos.grupo_id = ?
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);

$stmt->close();
$conn->close();
?>