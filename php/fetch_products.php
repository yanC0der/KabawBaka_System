<?php
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT product_id, product_name, category, price, image FROM products ORDER BY date_added DESC";
$result = $conn->query($sql);

$products = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
?>
