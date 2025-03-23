<?php
session_start();
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombreGrupo = $data['nombreGrupo'];
$descripcionGrupo = $data['descripcionGrupo'];
$miembros = $data['miembros'];
$current_user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO Grupos (nombre, descripcion, fecha_creacion) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $nombreGrupo, $descripcionGrupo);
    $stmt->execute();
    $grupo_id = $stmt->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Usuarios_Grupos (usuario_id, grupo_id) VALUES (?, ?)");
    foreach ($miembros as $miembro_id) {
        $stmt->bind_param("ii", $miembro_id, $grupo_id);
        $stmt->execute();
    }
    $stmt->bind_param("ii", $current_user_id, $grupo_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>