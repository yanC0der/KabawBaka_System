<?php
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Get total livestock
    $livestockQuery = "SELECT COUNT(*) as total FROM livestock";
    $livestockResult = $conn->query($livestockQuery);
    $totalLivestock = $livestockResult->fetch_assoc()['total'];

    // Get total users
    $usersQuery = "SELECT COUNT(*) as total FROM users WHERE role != 'admin'";
    $usersResult = $conn->query($usersQuery);
    $totalUsers = $usersResult->fetch_assoc()['total'];

    // Get total orders
    $ordersQuery = "SELECT COUNT(*) as total FROM orders";
    $ordersResult = $conn->query($ordersQuery);
    $totalOrders = $ordersResult->fetch_assoc()['total'];

    echo json_encode([
        'totalLivestock' => $totalLivestock,
        'totalUsers' => $totalUsers,
        'totalOrders' => $totalOrders
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
