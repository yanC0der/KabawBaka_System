<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tips (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $content);

    if ($stmt->execute()) {
        header("Location: ../kabawbaka.html?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
