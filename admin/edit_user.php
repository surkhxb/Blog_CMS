<?php
session_start();
require_once '../config/db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../scripts/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "User ID missing!";
    exit();
}

$user_id = $_GET['id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];

    $sql_update = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$role, $user_id]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/edit_user.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="overlay"></div>
    <div class="modal-container">
        <h1>Edit User</h1>
        <form method="POST">
            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="author" <?php echo ($user['role'] == 'author') ? 'selected' : ''; ?>>Author</option>
                <option value="reader" <?php echo ($user['role'] == 'reader') ? 'selected' : ''; ?>>Reader</option>
            </select>
            <button type="submit">Update Role</button>
        </form>
    </div>
</body>
</html>

