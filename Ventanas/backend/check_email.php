<?php
include 'db_connection.php';

$email = $_GET['email'] ?? '';

$response = ['in_use' => false];

if ($email) {
    $stmt = $conn->prepare("SELECT id FROM Usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['in_use'] = true;
    }

    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>