<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit();
}

// Check if product exists and has stock
$stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error (prepare)']);
    exit();
}
$stmt->bind_param("i", $product_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error (execute)']);
    exit();
}
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

if ($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit();
}

// Check if item already in cart
$stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
if (!$stmt) {
    $dberr = $conn->error ?: 'unknown';
    echo json_encode(['success' => false, 'message' => 'Database error (prepare select cart): ' . $dberr]);
    exit();
}
$stmt->bind_param("ii", $user_id, $product_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error (execute select cart)']);
    exit();
}
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity
    $cart_item = $result->fetch_assoc();
    $new_quantity = $cart_item['quantity'] + $quantity;

    if ($product['stock'] < $new_quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock for additional quantity']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error (prepare update cart)']);
        exit();
    }
    $stmt->bind_param("ii", $new_quantity, $cart_item['cart_id']);
} else {
    // Add new item
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error (prepare insert cart)']);
        exit();
    }
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    // Return DB error message for debugging (non-sensitive)
    $err = $stmt->error ?: $conn->error;
    echo json_encode(['success' => false, 'message' => 'Failed to add to cart: ' . $err]);
}
?>
