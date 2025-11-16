<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT o.order_id, o.quantity, o.total_price, o.status, o.order_date, p.product_name
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        WHERE o.buyer_id = ?
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = array();

while($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
