<?php
session_start();
include 'db_connection.php';

$current_user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT Chats.id, 
           CASE 
               WHEN Chats.usuario1_id = ? THEN usuario2_id 
               ELSE usuario1_id 
           END AS user_id, 
           (SELECT nombre_usuario FROM Usuarios WHERE id = user_id) AS nombre_usuario, 
           (SELECT foto_perfil FROM Usuarios WHERE id = user_id) AS foto_perfil,
           (SELECT marco_perfil FROM Usuarios WHERE id = user_id) AS marco_perfil,
           (SELECT Mensajes.contenido FROM Mensajes WHERE Mensajes.chat_id = Chats.id ORDER BY Mensajes.fecha_envio DESC LIMIT 1) AS ultimo_mensaje,
           (SELECT Mensajes.fecha_envio FROM Mensajes WHERE Mensajes.chat_id = Chats.id ORDER BY Mensajes.fecha_envio DESC LIMIT 1) AS fecha_ultimo_mensaje,
           (SELECT Usuarios.nombre_usuario 
            FROM Usuarios 
            JOIN Mensajes ON Usuarios.id = Mensajes.usuario_id 
            WHERE Mensajes.chat_id = Chats.id 
            ORDER BY Mensajes.fecha_envio DESC 
            LIMIT 1) AS nombre_usuario_mensaje
    FROM Chats 
    WHERE (Chats.usuario1_id = ? OR Chats.usuario2_id = ?)
    GROUP BY Chats.id
    ORDER BY fecha_ultimo_mensaje DESC
");
$stmt->bind_param("iii", $current_user_id, $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$chats = [];
while ($row = $result->fetch_assoc()) {
    $chats[] = $row;
}

$stmt->close();
$conn->close();
?>