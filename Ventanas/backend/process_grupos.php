<?php
session_start();
include 'db_connection.php';

$current_user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT Grupos.id, Grupos.nombre, Grupos.descripcion
    FROM Grupos
    JOIN Usuarios_Grupos ON Grupos.id = Usuarios_Grupos.grupo_id
    WHERE Usuarios_Grupos.usuario_id = ?
");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$grupos = [];
while ($row = $result->fetch_assoc()) {
    $grupos[] = $row;
}

$stmt->close();
$conn->close();
?>