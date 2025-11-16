<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kabawbaka_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Don't emit HTML or die here; let calling scripts handle the error and return JSON.
if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    // leave $conn set (with error) so callers can check $conn->connect_error
}
?>
