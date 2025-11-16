<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $seller_id = $_SESSION['user_id'];

    // Handle image upload
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
                echo "Sorry, there was an error uploading your file.";
                exit();
            }
        } else {
            echo "File is not an image.";
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (seller_id, product_name, category, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdss", $seller_id, $product_name, $category, $description, $price, $stock, $image);

    if ($stmt->execute()) {
        header("Location: ../marketplace.html");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product | KabawBaka?</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <!-- 🌾 Navbar -->
  <header>
    <div class="logo">
      <img src="../assets/kabawbaka-logo.png" alt="KabawBaka Logo">
      <h2>KabawBaka?</h2>
    </div>
    <nav>
      <ul>
        <li><a href="../index.html">Home</a></li>
        <li><a href="../marketplace.html">Marketplace</a></li>
        <li><a href="../kabawbaka.html">KabawBaka?</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </nav>
    <a href="../login.html" class="btn-login">Login</a>
  </header>

  <!-- 🌾 Add Product Section -->
  <section class="register-section">
    <div class="register-container">
      <h1>Add New Product</h1>
      <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="product_name" placeholder="Product Name" required>
        <select name="category" required>
          <option value="">Select Category</option>
          <option value="Feeds">Feeds</option>
          <option value="Livestock">Livestock</option>
          <option value="Tools">Tools</option>
          <option value="Medicines">Medicines</option>
        </select>
        <textarea name="description" placeholder="Description" rows="4"></textarea>
        <input type="number" name="price" placeholder="Price (₱)" step="0.01" required>
        <input type="number" name="stock" placeholder="Stock Quantity" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" class="btn-primary">Add Product</button>
      </form>
    </div>
  </section>

  <!-- 🌾 Footer -->
  <footer>
    <p>© 2025 KabawBaka | Innovative • Sustainable • Agripreneurial</p>
    <p>
      <a href="#">Facebook</a> |
      <a href="#">Instagram</a> |
      <a href="#">Contact Us</a>
    </p>
  </footer>

</body>
</html>
