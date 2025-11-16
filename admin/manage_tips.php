<?php
session_start();
include '../php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.html");
    exit();
}

// Handle delete tip
if (isset($_GET['delete'])) {
    $tip_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tips WHERE tip_id = ?");
    $stmt->bind_param("i", $tip_id);
    $stmt->execute();
    header("Location: manage_tips.php");
    exit();
}

// Fetch all tips
$sql = "SELECT t.*, u.full_name as user_name FROM tips t JOIN users u ON t.user_id = u.user_id ORDER BY t.date_posted DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Tips | KabawBaka? Admin</title>
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
    <h2>Manage Tips</h2>

    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Content</th>
          <th>Author</th>
          <th>Date Posted</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['tip_id']; ?></td>
          <td><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . (strlen($row['content']) > 100 ? '...' : ''); ?></td>
          <td><?php echo htmlspecialchars($row['user_name']); ?></td>
          <td><?php echo date('M d, Y', strtotime($row['date_posted'])); ?></td>
          <td>
            <a href="edit_tip.php?id=<?php echo $row['tip_id']; ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?php echo $row['tip_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this tip?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
