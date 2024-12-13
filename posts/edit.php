<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../scripts/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Post ID is missing!";
    exit();
}

$post_id = $_GET['id'];
$sql = "SELECT * FROM posts WHERE post_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $body_text = $_POST['body_text'];

    $sql_update = "UPDATE posts SET title = ?, body_text = ? WHERE post_id = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$title, $body_text, $post_id]);

    header("Location: view.php?id=$post_id");
    exit();
}
?>

<h1>Edit Post</h1>
<form method="POST">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>

    <label for="body_text">Body:</label>
    <textarea name="body_text" id="body_text" rows="10" required><?php echo htmlspecialchars($post['body_text']); ?></textarea><br>

    <button type="submit">Update Post</button>
</form>
