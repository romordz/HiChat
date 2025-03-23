<?php
$servername = "localhost";
$username = "root";
$password = "abc123";
$dbname = "POI";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>