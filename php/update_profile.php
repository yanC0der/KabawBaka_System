<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// Validate input
if (empty($full_name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Full name and email are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Check if email is already taken by another user
$email_check_sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
$stmt = $conn->prepare($email_check_sql);
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already in use']);
    exit();
}

// Prepare update query
$update_fields = ["full_name = ?", "email = ?", "contact_number = ?", "address = ?"];
$update_values = [$full_name, $email, $contact_number, $address];
$types = "ssss";

// Handle password change
if (!empty($new_password)) {
    if (empty($current_password)) {
        echo json_encode(['success' => false, 'message' => 'Current password is required to change password']);
        exit();
    }

    // Verify current password
    $password_sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($password_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current_password, $user['password']) && $current_password !== $user['password']) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit();
    }

    $update_fields[] = "password = ?";
    $update_values[] = password_hash($new_password, PASSWORD_DEFAULT);
    $types .= "s";
}

$update_sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
$update_values[] = $user_id;
$types .= "i";

$stmt = $conn->prepare($update_sql);
$stmt->bind_param($types, ...$update_values);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>
