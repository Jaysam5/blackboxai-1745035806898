<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About - Campus Lost and Found</title>
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
    <h2>About the Campus Lost and Found System</h2>
    <p>This system is designed to help students, staff, and visitors report and find lost and found items on campus efficiently.</p>
    <p>Users can register, login, and manage their lost or found items through a secure dashboard. Admins have additional privileges to manage all items and users.</p>
    <p>Our goal is to make the campus a safer and more organized place by reducing lost item issues.</p>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Campus Lost and Found System</p>
</footer>
</body>
</html>
