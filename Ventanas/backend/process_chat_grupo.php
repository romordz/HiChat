<?php
session_start();
include 'db_connection.php';

$grupo_id = $_GET['grupo_id'] ?? null;
if (!$grupo_id) {
    die("Grupo no especificado.");
}

$current_user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT Grupos.nombre, Grupos.descripcion
    FROM Grupos
    JOIN Usuarios_Grupos ON Grupos.id = Usuarios_Grupos.grupo_id
    WHERE Grupos.id = ? AND Usuarios_Grupos.usuario_id = ?
");
$stmt->bind_param("ii", $grupo_id, $current_user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("No tienes acceso a este grupo.");
}
$stmt->bind_result($nombre_grupo, $descripcion_grupo);
$stmt->fetch();
$stmt->close();
?>