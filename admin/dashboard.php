<?php
session_start();
require_once '../config/db.php'; // Include the database connection

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: ../index.php"); // Redirect to index.php
    exit();
}

// Ensure the user is logged in and has an 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../scripts/login.php");
    exit(); // Ensure no further code is executed
}

// Fetch all users from the database
$sql_users = "SELECT * FROM users";
$stmt_users = $pdo->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Fetch all posts with their author names
$sql_posts = "
    SELECT posts.*, users.username AS author_name
    FROM posts
    JOIN users ON posts.author_id = users.user_id";
$stmt_posts = $pdo->prepare($sql_posts);
$stmt_posts->execute();
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

// Fetch all comments (both pending and approved)
$sql_comments = "
    SELECT comments.*, posts.title AS post_title, users.username AS commenter_name 
    FROM comments 
    JOIN posts ON comments.post_id = posts.post_id 
    JOIN users ON comments.user_id = users.user_id";
$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute();
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// Handle user deletion
if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];

    // Fetch the role of the user to prevent authors from deleting their own account
    $sql_check_role = "SELECT role FROM users WHERE user_id = ?";
    $stmt_check_role = $pdo->prepare($sql_check_role);
    $stmt_check_role->execute([$user_id]);
    $user = $stmt_check_role->fetch(PDO::FETCH_ASSOC);

    if ($user['role'] !== 'author' || $_SESSION['role'] === 'admin') {
        // Delete all posts associated with the user first
        $sql_delete_posts = "DELETE FROM posts WHERE author_id = ?";
        $stmt_delete_posts = $pdo->prepare($sql_delete_posts);
        $stmt_delete_posts->execute([$user_id]);

        // Now, delete the user
        $sql_delete_user = "DELETE FROM users WHERE user_id = ?";
        $stmt_delete_user = $pdo->prepare($sql_delete_user);
        $stmt_delete_user->execute([$user_id]);

        header("Location: dashboard.php"); // Redirect back to the dashboard after deletion
        exit();
    } else {
        echo "You cannot delete your own account as an author.";
    }
}

// Handle post approval (set publish_status to 'published')
if (isset($_GET['approve_post_id'])) {
    $post_id = $_GET['approve_post_id'];

    $sql_approve = "UPDATE posts SET publish_status = 'published' WHERE post_id = ?";
    $stmt_approve = $pdo->prepare($sql_approve);
    $stmt_approve->execute([$post_id]);

    header("Location: dashboard.php");
    exit();
}

// Handle post deletion
if (isset($_GET['delete_post_id'])) {
    $post_id = $_GET['delete_post_id'];

    $sql_delete_post = "DELETE FROM posts WHERE post_id = ?";
    $stmt_delete_post = $pdo->prepare($sql_delete_post);
    $stmt_delete_post->execute([$post_id]);

    header("Location: dashboard.php");
    exit();
}

// Handle comment approval
if (isset($_GET['approve_comment_id'])) {
    $comment_id = $_GET['approve_comment_id'];

    $sql_approve_comment = "UPDATE comments SET moderation_status = 'approved' WHERE comment_id = ?";
    $stmt_approve_comment = $pdo->prepare($sql_approve_comment);
    $stmt_approve_comment->execute([$comment_id]);

    header("Location: dashboard.php");
    exit();
}

// Handle comment rejection
if (isset($_GET['reject_comment_id'])) {
    $comment_id = $_GET['reject_comment_id'];

    $sql_reject_comment = "UPDATE comments SET moderation_status = 'rejected' WHERE comment_id = ?";
    $stmt_reject_comment = $pdo->prepare($sql_reject_comment);
    $stmt_reject_comment->execute([$comment_id]);

    header("Location: dashboard.php");
    exit();
}

// Handle comment deletion
if (isset($_GET['delete_comment_id'])) {
    $comment_id = $_GET['delete_comment_id'];

    $sql_delete_comment = "DELETE FROM comments WHERE comment_id = ?";
    $stmt_delete_comment = $pdo->prepare($sql_delete_comment);
    $stmt_delete_comment->execute([$comment_id]);

    header("Location: dashboard.php");
    exit();
}
?>

<main>
    <h1>Admin Dashboard</h1>
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- Logout Button -->
    <form method="GET" style="text-align: right; margin-bottom: 20px;">
        <button type="submit" name="logout" style="background-color: #f44336; color: white; border: none; padding: 10px 20px; cursor: pointer;">
            Logout
        </button>
    </form>

    <!-- Manage Users Section -->
    <section>
        <h2>Manage Users</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Account Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['account_status']); ?></td>
                    <td>
                        <?php if ($user['role'] !== 'author' || $_SESSION['role'] === 'admin'): ?>
                            <a href="?delete_user_id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user and their posts?');">Delete</a> |
                        <?php endif; ?>
                        <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <!-- Manage Posts Section -->
    <section>
        <h2>Manage Posts</h2>
        <table border="1">
            <tr>
                <th>Post ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo $post['post_id']; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                    <td><?php echo htmlspecialchars($post['publish_status']); ?></td>
                    <td>
                        <a href="?delete_post_id=<?php echo $post['post_id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a> |
                        <?php if ($post['publish_status'] !== 'published'): ?>
                            <a href="?approve_post_id=<?php echo $post['post_id']; ?>">Approve</a>
                        <?php else: ?>
                            Already Published
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <!-- Manage Comments Section -->
    <section>
        <h2>Manage Comments</h2>
        <table border="1">
            <tr>
                <th>Comment ID</th>
                <th>Post</th>
                <th>Commenter</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?php echo $comment['comment_id']; ?></td>
                    <td><?php echo htmlspecialchars($comment['post_title']); ?></td>
                    <td><?php echo htmlspecialchars($comment['commenter_name']); ?></td>
                    <td><?php echo htmlspecialchars($comment['comment_text']); ?></td>
                    <td><?php echo htmlspecialchars($comment['moderation_status']); ?></td>
                    <td>
                        <?php if ($comment['moderation_status'] !== 'approved'): ?>
                            <a href="?approve_comment_id=<?php echo $comment['comment_id']; ?>">Approve</a> |
                        <?php endif; ?>
                        <a href="?reject_comment_id=<?php echo $comment['comment_id']; ?>" onclick="return confirm('Are you sure you want to reject this comment?');">Reject</a> |
                        <a href="?delete_comment_id=<?php echo $comment['comment_id']; ?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>

<?php include '../includes/footer.php'; ?> <!-- Include the footer at the end of the page -->
