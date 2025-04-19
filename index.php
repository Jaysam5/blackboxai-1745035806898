<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Campus Lost and Found - Home</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
    <h1>Campus Lost and Found System</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        <?php endif; ?>
        <a href="about.php">About</a>
    </nav>
</header>
<div class="container">
    <h2>Welcome to the Campus Lost and Found System</h2>
    <p>This system helps you report and find lost and found items on campus.</p>
    <?php if (!isset($_SESSION['username'])): ?>
        <p>Please <a href="login.php">login</a> or <a href="signup.php">sign up</a> to get started.</p>
    <?php else: ?>
        <p>Go to your <a href="dashboard.php">dashboard</a> to manage your lost and found items.</p>
    <?php endif; ?>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Campus Lost and Found System</p>
</footer>
</body>
</html>
