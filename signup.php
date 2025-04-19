<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($role, ['user', 'admin'])) {
        $error = 'Invalid role selected.';
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Insert new user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, email) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $username, $password_hash, $role, $email);
            if ($insert_stmt->execute()) {
                $success = 'Registration successful. You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Error occurred during registration.';
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Signup - Campus Lost and Found</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
    <h1>Campus Lost and Found System</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="about.php">About</a>
    </nav>
</header>
<div class="container">
    <h2>Signup</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="post" action="signup.php" novalidate>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required />

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="user" <?php if (($_POST['role'] ?? '') === 'user') echo 'selected'; ?>>User</option>
            <option value="admin" <?php if (($_POST['role'] ?? '') === 'admin') echo 'selected'; ?>>Admin</option>
        </select>

        <button type="submit">Signup</button>
    </form>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Campus Lost and Found System</p>
</footer>
</body>
</html>
