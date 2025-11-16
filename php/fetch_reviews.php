<?php
include 'db_connect.php';

header('Content-Type: application/json');

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit();
}

$sql = "SELECT r.*, u.full_name FROM product_reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.product_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = array();
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
?>
