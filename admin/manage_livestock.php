<?php
session_start();
include '../php/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.html");
    exit();
}

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

$query = "SELECT * FROM livestock WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ? OR health_status LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "sss";
}
if (!empty($typeFilter)) {
    $query .= " AND type = ?";
    $params[] = $typeFilter;
    $types .= "s";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Livestock | KabawBaka Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f6f3;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #144d1e;
            color: white;
            padding: 15px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header img {
            height: 60px;
        }

        header h1 {
            font-size: 24px;
        }

        .container {
            padding: 30px 60px;
        }

        h2 {
            color: #144d1e;
        }

        /* Search and Filter Bar */
        .search-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .search-filter input, 
        .search-filter select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        .search-filter button {
            background-color: #1c6b2f;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
        }

        .search-filter button:hover {
            background-color: #155c26;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #144d1e;
            color: white;
        }

        tr:hover {
            background-color: #eef8f0;
        }

        img {
            width: 100px;
            border-radius: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin: 0 2px;
        }

        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #da190b;
        }
        .search-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin: 20px 30px;
        }

        .search-filter-bar input,
        .search-filter-bar select {
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            background: #2c5e2c;
            color: #fff;
            font-size: 1rem;
            width: 48%;
        }

        .search-filter-bar input::placeholder {
            color: #b6dab6;
        }

        .search-filter-bar input:focus,
        .search-filter-bar select:focus {
            outline: 2px solid #6bff91;
        }
    </style>
    
</head>
<body>

    <header>
        <img src="assets/logo.png" alt="KabawBaka Logo">
        <h1>Manage Livestock</h1>
    </header>
    <div class="search-filter-bar">
    <input type="text" id="searchInput" placeholder="🔍 Search by name or type...">
    <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Healthy">Healthy</option>
        <option value="Sick">Sick</option>
        <option value="Under Observation">Under Observation</option>
    </select>
    </div>
    <div class="container">
        <h2>Livestock Overview</h2>

        <form method="GET" class="search-filter">
            <input type="text" name="search" placeholder="Search livestock..." value="<?= htmlspecialchars($search) ?>">
            <select name="type">
                <option value="">All Types</option>
                <option value="Cow" <?= $typeFilter == 'Cow' ? 'selected' : '' ?>>Cow</option>
                <option value="Carabao" <?= $typeFilter == 'Carabao' ? 'selected' : '' ?>>Carabao</option>
                <option value="Pig" <?= $typeFilter == 'Pig' ? 'selected' : '' ?>>Pig</option>
                <option value="Chicken" <?= $typeFilter == 'Chicken' ? 'selected' : '' ?>>Chicken</option>
                <option value="Goat" <?= $typeFilter == 'Goat' ? 'selected' : '' ?>>Goat</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Livestock Name</th>
                    <th>Type</th>
                    <th>Health Status</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="../uploads/<?= $row['image'] ?>" alt="Livestock Image"></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['type'] ?></td>
                    <td><?= $row['health_status'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td>
                        <a href="edit_livestock.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                        <a href="delete_livestock.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this livestock?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const cards = document.querySelectorAll('.livestock-card');

    function filterCards() {
        const searchText = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        cards.forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        const type = card.querySelector('p').textContent.toLowerCase();
        const status = card.querySelector('.status').textContent;

        const matchesSearch = name.includes(searchText) || type.includes(searchText);
        const matchesStatus = statusValue === "" || status === statusValue;

        if (matchesSearch && matchesStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
        });
    }

    searchInput.addEventListener('keyup', filterCards);
    statusFilter.addEventListener('change', filterCards);
    });
    </script>

</body>
</html>
