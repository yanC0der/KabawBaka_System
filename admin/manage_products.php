<?php
session_start();
include '../php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.html");
    exit();
}

// Handle delete product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

// Fetch all products
$sql = "SELECT p.*, u.full_name as seller_name FROM products p JOIN users u ON p.seller_id = u.user_id ORDER BY p.date_added DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Products | KabawBaka? Admin</title>
  <link rel="stylesheet" href="admin.css" />
</head>
<body class="dashboard-body">
  <header class="admin-header">
    <img src="../assets/kabawbaka-logo.png" alt="Logo" class="admin-logo-small">
    <h1>KabawBaka? Admin Dashboard</h1>
  </header>

  <nav class="sidebar">
    <ul>
      <li><a href="admin-dashboard.html">📊 Dashboard</a></li>
      <li><a href="manage_livestock.php">🐄 Manage Livestock</a></li>
      <li><a href="manage_products.php">📦 Manage Products</a></li>
      <li><a href="manage_tips.php">💬 Manage Tips</a></li>
      <li><a href="manage_users.php">👤 Users</a></li>
      <li><a href="manage_orders.php">🧾 Orders</a></li>
      <li><a href="announcements.php">📢 Announcements</a></li>
      <li><a href="settings.php">⚙️ Settings</a></li>
      <li class="logout"><a href="admin-login.html">🚪 Logout</a></li>
    </ul>
  </nav>

  <main class="main-content">
    <h2>Manage Products</h2>
    <a href="../php/add_product.php" class="btn-primary">Add New Product</a>

    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Product Name</th>
          <th>Category</th>
          <th>Seller</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['product_id']; ?></td>
          <td><?php echo htmlspecialchars($row['product_name']); ?></td>
          <td><?php echo $row['category']; ?></td>
          <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
          <td>₱<?php echo number_format($row['price'], 2); ?></td>
          <td><?php echo $row['stock']; ?></td>
          <td>
            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?php echo $row['product_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
