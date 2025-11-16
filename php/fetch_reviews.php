<?php
include 'db_connect.php';

header('Content-Type: application/json; charset=UTF-8');

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

if (!isset($conn) || (isset($conn) && $conn->connect_error)) {
    $msg = isset($conn) ? $conn->connect_error : 'No DB connection';
    echo json_encode(['success' => false, 'message' => 'Database connection error: ' . $msg]);
    exit();
}

$sql = "SELECT r.*, u.full_name FROM product_reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.product_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error (prepare): ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $product_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error (execute): ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();

$reviews = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

echo json_encode(['success' => true, 'reviews' => $reviews]);
?>
