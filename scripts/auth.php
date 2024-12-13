<?php
session_start();
require_once '../config/db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the selected role from the form

    // Validate the role value
    $allowed_roles = ['author', 'reader']; // Allowed roles
    if (!in_array($role, $allowed_roles)) {
        echo "Error: Invalid role selected.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$username, $email, $hashed_password, $role]);
        $_SESSION['user_id'] = $pdo->lastInsertId();  // Get the last inserted user ID
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect based on the role
        if ($role === 'author') {
            header("Location: ../posts/create.php");  // Author redirect
        } else {
            header("Location: ../index.php");  // Reader redirect
        }
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/register_style.css">
    <script src="../assets/js/auth_formValidation.js"></script>

    <title>Register</title>
</head>
<body>
    <div id="webcrumbs"> 
        <div class="w-[500px] bg-white rounded-lg shadow-lg p-8 flex flex-col gap-6">  
            <h1 class="text-2xl font-title text-neutral-950">Register</h1>
            <form action="auth.php" method="POST" class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <label class="text-neutral-700 font-medium mb-2" for="username">Username <span class="required" style="color: #ff4d4d; font-weight: bold;">*</span></label>
                    <input id="username" name="username" type="text" class="w-full border border-neutral-300 rounded-md p-3 text-neutral-950" placeholder="Enter your Username" required>
                </div>
                <div class="flex flex-col">
                    <label class="text-neutral-700 font-medium mb-2" for="email">Email <span class="required" style="color: #ff4d4d; font-weight: bold;">*</span></label>
                    <input id="email" name="email" type="email" class="w-full border border-neutral-300 rounded-md p-3 text-neutral-950" placeholder="Enter your email" required>
                </div>
                <div class="flex flex-col">
                    <label class="text-neutral-700 font-medium mb-2" for="password">Password <span class="required" style="color: #ff4d4d; font-weight: bold;">*</span></label>
                    <input id="password" name="password" type="password" class="w-full border border-neutral-300 rounded-md p-3 text-neutral-950" placeholder="Enter your password" required>
                </div>
                <div class="flex flex-col">
                    <label class="text-neutral-700 font-medium mb-2" for="role">Select Role <span class="required" style="color: #ff4d4d; font-weight: bold;">*</span></label>
                    <select id="role" name="role" class="w-full border border-neutral-300 rounded-md p-3 text-neutral-950" required>
                        <option value="author">Author</option>
                        <option value="reader">Reader</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary-500 text-primary-50 font-medium py-3 rounded-md hover:bg-primary-600 transition duration-200">
                    Register
                </button>
            </form>
            <p class="text-center text-neutral-700">
                Already have an account? <a href="../scripts/login.php" class="text-primary-500 font-medium hover:underline">Login here</a>
            </p>
        </div> 
    </div>
</body>
</html>
