<?php
session_start();
include '../php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.html");
    exit();
}

if (isset($_POST['add_livestock'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $health_status = $_POST['health_status'];
    $description = $_POST['description'];

    // Image upload handling
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = basename($_FILES["image"]["name"]);
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
            exit();
        }
    }

    // Insert query with prepared statement
    $stmt = $conn->prepare("INSERT INTO livestock (name, type, health_status, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $type, $health_status, $description, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Livestock added successfully!'); window.location.href='manage_livestock.php';</script>";
    } else {
        echo "<script>alert('Error adding livestock: " . $stmt->error . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Livestock | KabawBaka Admin</title>
  <style>
    :root {
      --primary: #1e4d2b;
      --accent: #81c784;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f8f6;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .form-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
      width: 420px;
      text-align: center;
    }

    h1 {
      color: var(--primary);
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    input, select {
      padding: 10px 14px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      transition: 0.2s;
    }

    input:focus, select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 6px rgba(129, 199, 132, 0.4);
    }

    .btn {
      background-color: var(--primary);
      color: #fff;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      background-color: var(--accent);
      color: #1e4d2b;
    }

    .back-link {
      display: block;
      margin-top: 16px;
      color: #1e4d2b;
      text-decoration: none;
      font-size: 14px;
    }

    .back-link:hover {
      text-decoration: underline;
    }
    .image-preview-container {
  margin-top: 10px;
  display: flex;
  justify-content: center;
}

.image-preview-container img {
  max-width: 200px;
  max-height: 200px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid #1e4d2b;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

  </style>
</head>
<body>
  <div class="form-container">
    <h1>🐮 Add New Livestock</h1>

    <form method="POST" enctype="multipart/form-data" class="livestock-form">
      <h3>Add New Livestock</h3>

      <label for="name">Livestock Name:</label>
      <input type="text" id="name" name="name" placeholder="Enter livestock name" required>

      <label for="type">Type:</label>
        <select id="type" name="type" required>
          <option value="">Select type</option>
          <option value="Cattle">Cattle</option>
          <option value="Carabao">Carabao</option>
          <option value="Pig">Pig</option>
          <option value="Goat">Goat</option>
          <option value="Chicken">Chicken</option>
          <option value="Duck">Duck</option>
          <option value="Others">Others</option>
        </select>

      <label for="health_status">Health Status:</label>
        <select id="health_status" name="health_status" required>
          <option value="Healthy">Healthy</option>
          <option value="Sick">Sick</option>
          <option value="Under Observation">Under Observation</option>
        </select>

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="3" placeholder="Enter short description or remarks"></textarea>

      <label for="image">Upload Image:</label>
      <input type="file" id="image" name="image" accept="image/*" required>

      <button type="submit" name="add_livestock">Add Livestock</button>
    </form>

    <a href="manage_livestock.php" class="back-link">← Back to Livestock List</a>
  </div>
  <script>
function previewImage(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('preview');

  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = '';
    preview.style.display = 'none';
  }
}
</script>

</body>
</html>
