<?php
session_start();
include '../php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.html");
    exit();
}

if (!isset($_GET['id'])) {
    die("Livestock ID is missing.");
}

$id = $_GET['id'];

// Get image name before deleting
$result = mysqli_query($conn, "SELECT image FROM livestock WHERE id = $id");
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $image_path = "../uploads/" . $row['image'];

    // Delete the record
    if (mysqli_query($conn, "DELETE FROM livestock WHERE id = $id")) {
        // Delete the image file if it exists
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        echo "<script>alert('Livestock deleted successfully!'); window.location='manage_livestock.php';</script>";
    } else {
        echo "<script>alert('Error deleting livestock!'); window.location='manage_livestock.php';</script>";
    }
} else {
    echo "<script>alert('Livestock not found!'); window.location='manage_livestock.php';</script>";
}
?>
