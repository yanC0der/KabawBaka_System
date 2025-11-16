<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, c.added_at,
           p.product_id, p.product_name, p.price, p.image, p.stock,
           u.full_name as seller_name
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    JOIN users u ON p.seller_id = u.user_id
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

echo json_encode(['success' => true, 'cart' => $cart_items]);
?>
