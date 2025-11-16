<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to place an order']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $buyer_id = $_SESSION['user_id'];

    // Validate input
    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        exit();
    }

    // Get product details
    $product_sql = "SELECT price, stock FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($product_sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();

    if ($product_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }

    $product = $product_result->fetch_assoc();
    $total_price = $product['price'] * $quantity;

    // Check stock availability
    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
        exit();
    }

    // Insert order
    $order_sql = "INSERT INTO orders (buyer_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("iiid", $buyer_id, $product_id, $quantity, $total_price);

    if ($stmt->execute()) {
        // Update stock
        $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
        $stmt = $conn->prepare($update_stock_sql);
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to place order']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
