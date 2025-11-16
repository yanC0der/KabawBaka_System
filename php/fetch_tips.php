<?php
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT t.content, t.created_at, u.full_name FROM tips t JOIN users u ON t.user_id = u.user_id ORDER BY t.created_at DESC";
$result = $conn->query($sql);

$tips = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tips[] = $row;
    }
}

echo json_encode($tips);
?>
