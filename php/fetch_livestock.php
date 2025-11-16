<?php
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT id, name, owner_name, type, breed, age, health_status, description, image FROM livestock ORDER BY created_at DESC";
$result = $conn->query($sql);

$livestock = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $livestock[] = $row;
    }
}

echo json_encode($livestock);
?>
