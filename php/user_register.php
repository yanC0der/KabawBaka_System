<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, contact_number, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $email, $password, $role, $contact_number, $address);

    if ($stmt->execute()) {
        header("Location: ../login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
