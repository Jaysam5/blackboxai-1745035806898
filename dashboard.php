<?php
session_start();
require_once 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$username = $_SESSION['username'];

$error = '';
$success = '';

// Handle new item submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $type = $_POST['type'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? '';

    if (empty($type) || empty($title) || empty($description) || empty($date)) {
        $error = 'Please fill in all fields to add an item.';
    } elseif (!in_array($type, ['lost', 'found'])) {
        $error = 'Invalid item type.';
    } else {
        $stmt = $conn->prepare("INSERT INTO items (user_id, type, title, description, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $type, $title, $description, $date);
        if ($stmt->execute()) {
            $success = 'Item added successfully.';
        } else {
            $error = 'Failed to add item.';
        }
        $stmt->close();
    }
}

// Handle item status update (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status']) && $user_role === 'admin') {
    $item_id = intval($_POST['item_id']);
    $new_status = $_POST['status'] ?? '';
    if (in_array($new_status, ['open', 'claimed'])) {
        $stmt = $conn->prepare("UPDATE items SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $item_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch items
if ($user_role === 'admin') {
    $items_result = $conn->query("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id ORDER BY items.created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id WHERE items.user_id = ? ORDER BY items.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Campus Lost and Found</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
    <h1>Campus Lost and Found System</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout (<?php echo htmlspecialchars($username); ?>)</a>
        <a href="about.php">About</a>
    </nav>
</header>
<div class="container">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($username); ?>! You are logged in as <strong><?php echo htmlspecialchars($user_role); ?></strong>.</p>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <h3>Add New Item</h3>
    <form method="post" action="dashboard.php" novalidate>
        <input type="hidden" name="add_item" value="1" />
        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="lost">Lost</option>
            <option value="found">Found</option>
        </select>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required />

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required />

        <button type="submit">Add Item</button>
    </form>

    <h3>Your Items</h3>
    <div class="item-list">
        <?php if ($items_result && $items_result->num_rows > 0): ?>
            <?php while ($item = $items_result->fetch_assoc()): ?>
                <div class="item">
                    <div class="item-title"><?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['type']); ?>)</div>
                    <div class="item-meta">Reported by: <?php echo htmlspecialchars($item['username']); ?> | Date: <?php echo htmlspecialchars($item['date']); ?> | Status: <?php echo htmlspecialchars($item['status']); ?></div>
                    <div class="item-description"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
                    <?php if ($user_role === 'admin'): ?>
                        <form method="post" action="dashboard.php" style="margin-top: 0.5rem;">
                            <input type="hidden" name="update_status" value="1" />
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
                            <label for="status_<?php echo $item['id']; ?>">Update Status:</label>
                            <select id="status_<?php echo $item['id']; ?>" name="status">
                                <option value="open" <?php if ($item['status'] === 'open') echo 'selected'; ?>>Open</option>
                                <option value="claimed" <?php if ($item['status'] === 'claimed') echo 'selected'; ?>>Claimed</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items found.</p>
        <?php endif; ?>
    </div>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Campus Lost and Found System</p>
</footer>
</body>
</html>
<?php
if ($user_role !== 'admin') {
    $stmt->close();
}
$conn->close();
?>
