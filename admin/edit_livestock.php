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
$result = mysqli_query($conn, "SELECT * FROM livestock WHERE id = $id");

if (!$result || mysqli_num_rows($result) == 0) {
    die("Livestock not found.");
}

$row = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $health_status = $_POST['health_status'];
    $description = $_POST['description'];

    // Image upload
    if ($_FILES['image']['name']) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image_name = $row['image']; // keep old image if not replaced
    }

    $update = "UPDATE livestock SET name=?, type=?, health_status=?, description=?, image=? WHERE id=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sssssi", $name, $type, $health_status, $description, $image_name, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Livestock updated successfully!'); window.location='manage_livestock.php';</script>";
    } else {
        echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Livestock</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #0b2e13;
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            background: #14532d;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #d1fae5;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        input, select, textarea {
            padding: 10px;
            border-radius: 8px;
            border: none;
            width: 100%;
            background: #1a4d2e;
            color: white;
        }
        input[type="file"] {
            background: none;
        }
        .btn {
            background-color: #22c55e;
            border: none;
            padding: 12px;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background-color: #16a34a;
        }
        .preview {
            text-align: center;
        }
        .preview img {
            max-width: 200px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Livestock</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>

            <label for="type">Type:</label>
            <input type="text" name="type" value="<?php echo htmlspecialchars($row['type']); ?>" required>

            <label for="health_status">Health Status:</label>
            <select name="health_status">
                <option value="Healthy" <?php if ($row['health_status'] == 'Healthy') echo 'selected'; ?>>Healthy</option>
                <option value="Sick" <?php if ($row['health_status'] == 'Sick') echo 'selected'; ?>>Sick</option>
                <option value="Under Observation" <?php if ($row['health_status'] == 'Under Observation') echo 'selected'; ?>>Under Observation</option>
            </select>

            <label for="description">Description:</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($row['description']); ?></textarea>

            <label for="image">Current Image:</label>
            <div class="preview">
                <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?php echo $row['image']; ?>" alt="Current Image">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>

            <label for="image">Upload New Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</body>
</html>
