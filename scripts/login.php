<?php
session_start();
require_once '../config/db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query to fetch the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and the password matches
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on the role
        if ($_SESSION['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");  // Admin redirect
        } elseif ($_SESSION['role'] === 'author') {
            header("Location: ../posts/create.php");  // Author CRUD operations
        } elseif ($_SESSION['role'] === 'reader') {
            header("Location: ../posts/latest_blogs.php");  // Reader redirect to latest blogs
        } else {
            header("Location: ../index.php");  // Default redirect for unknown roles
        }
        exit();  // Prevent further code execution
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login_style.css">
    <title>Login</title>
    <script>
        // Function to show the error alert if the error_message exists
        <?php if (isset($error_message)): ?>
            window.onload = function() {
                alert("<?php echo addslashes($error_message); ?>");
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <div id="login-card">
        <h1>Login</h1>
        <form action="login.php" method="POST">
            <div>
                <label for="username">Username <span class="required" style="color: #ff4d4d; font-weight: bold;">*</span></label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div>
                <label for="password">Password <span class="required" style="color: #ff4d4d; font-weight: bold;">*</span></label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>
            Don't have an account? <a href="../scripts/auth.php">Register here</a>
        </p>
    </div>
</body>
</html>
