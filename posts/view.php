<?php
session_start(); // Start the session
require_once '../config/db.php'; // Include the database connection

if (!isset($_GET['id'])) {
    $_SESSION['popup_message'] = 'Post ID is missing!';
    header("Location: some_page.php"); // Redirect to a relevant page
    exit();
}

$post_id = $_GET['id'];

if (isset($_SESSION['popup_message'])) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() {
        showPopup('" . addslashes($_SESSION['popup_message']) . "');
    });</script>";
    unset($_SESSION['popup_message']); // Clear the message after displaying
}

// Fetch the post details, including the image path
$sql = "SELECT posts.*, users.username AS author_name 
        FROM posts 
        JOIN users ON posts.author_id = users.user_id 
        WHERE post_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['popup_message'] = 'Post not found!';
    header("Location: some_page.php"); // Redirect to a relevant page
    exit();
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $user_id = $_SESSION['user_id'] ?? null; // Check if user is logged in
    $comment_text = trim($_POST['comment_text']);
    $creation_date = date('Y-m-d H:i:s');
    $moderation_status = 'approved'; // New comments are pending by default

    // CAPTCHA verification
    if (isset($_POST['captcha_answer']) && isset($_SESSION['captcha_question'])) {
        $correct_answer = $_SESSION['captcha_question']['answer'];
        $user_answer = intval($_POST['captcha_answer']);

        if ($user_answer !== $correct_answer) {
            $_SESSION['popup_message'] = 'Incorrect CAPTCHA. Please try again.';
        } elseif (!empty($comment_text) && $user_id) {
            try {
                $sql_insert_comment = "INSERT INTO comments (post_id, user_id, comment_text, creation_date, moderation_status) 
                                       VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $pdo->prepare($sql_insert_comment);
                $stmt_insert->execute([$post_id, $user_id, $comment_text, $creation_date, $moderation_status]);
                $_SESSION['popup_message'] = 'Comment submitted and awaiting moderation!';
            } catch (PDOException $e) {
                $_SESSION['popup_message'] = 'Error: ' . addslashes($e->getMessage());
            }
        } else {
            $_SESSION['popup_message'] = 'You must be logged in to comment, and your comment cannot be empty.';
        }
    } else {
        $_SESSION['popup_message'] = 'CAPTCHA is required.';
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $post_id);
    exit();
}

// Generate a new CAPTCHA question
$number1 = rand(1, 10);
$number2 = rand(1, 10);
$operation = rand(0, 1) === 0 ? '+' : '*';
$answer = $operation === '+' ? $number1 + $number2 : $number1 * $number2;
$_SESSION['captcha_question'] = [
    'question' => "$number1 $operation $number2 = ?",
    'answer' => $answer
];

// Fetch comments for this post
$sort_order = $_GET['sort'] ?? 'latest'; // Default sorting by latest
$order_by = $sort_order === 'oldest' ? 'ASC' : 'DESC';

$sql_comments = "SELECT comments.*, users.username AS commenter_name 
                 FROM comments 
                 JOIN users ON comments.user_id = users.user_id 
                 WHERE post_id = ? AND moderation_status = 'approved' 
                 ORDER BY creation_date $order_by";
$stmt_comments = $pdo->prepare($sql_comments);
$stmt_comments->execute([$post_id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// Add this section to handle the deletion of a comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment_id'])) {
    $comment_id = intval($_POST['delete_comment_id']);
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {
        $sql_delete_comment = "DELETE FROM comments WHERE comment_id = ? AND user_id = ?";
        $stmt_delete = $pdo->prepare($sql_delete_comment);
        if ($stmt_delete->execute([$comment_id, $user_id])) {
            $_SESSION['popup_message'] = 'Comment deleted successfully.';
        } else {
            $_SESSION['popup_message'] = 'Error deleting comment.';
        }
    } else {
        $_SESSION['popup_message'] = 'You must be logged in to delete comments.';
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $post_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/view.css?v=<?php echo time(); ?>">
</head>
<body>
<main class="post-container">
    <!-- Post Details -->
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <!-- Display the image if available -->
    <?php if (!empty($post['image_path'])): ?>
                        <img src="../<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image" class="post-image">
                    <?php endif; ?>
    <p class="post-body"><?php echo nl2br(htmlspecialchars($post['body_text'])); ?></p>
    <p class="post-meta"><em>Posted on <?php echo date('F j, Y', strtotime($post['creation_date'])); ?> by <?php echo htmlspecialchars($post['author_name']); ?></em></p>

    <!-- Comments Section -->
    <h2>Comments</h2>

    <!-- Sort Comments -->
    <form method="GET" action="view.php" class="sort-comments">
        <input type="hidden" name="id" value="<?php echo $post_id; ?>">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="latest" <?php if ($sort_order === 'latest') echo 'selected'; ?>>Latest</option>
            <option value="oldest" <?php if ($sort_order === 'oldest') echo 'selected'; ?>>Oldest</option>
        </select>
    </form>

    <!-- Display Comments -->
    <?php if (count($comments) > 0): ?>
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
    <div class="comment-left">
        <span class="comment-author"><strong><?php echo htmlspecialchars($comment['commenter_name']); ?>:</strong></span>
        <span class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></span>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
            <form method="POST" class="delete-comment-form">
                <input type="hidden" name="delete_comment_id" value="<?php echo $comment['comment_id']; ?>">
                <button type="button" class="delete-button" onclick="confirmDelete(this, event)">🗑️</button>
            </form>
        <?php endif; ?>
    </div>
    <span class="comment-meta"><em>Posted on <?php echo date('F j, Y, g:i a', strtotime($comment['creation_date'])); ?></em></span>
</div>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>No comments yet. Be the first to comment!</p>
<?php endif; ?>

    <!-- Add a New Comment -->
<h3>Leave a Comment</h3>
<form method="POST" class="new-comment-form">
    <textarea name="comment_text" rows="5" placeholder="Write your comment here..." required></textarea><br>
    <div class="captcha-container">
        <button type="button" onclick="showCaptchaBox()">Submit Comment</button>
        <div class="captcha-box" id="captcha-box">
            <label><?php echo $_SESSION['captcha_question']['question']; ?></label>
            <input type="number" name="captcha_answer" placeholder="Your answer" required>
            <button type="submit">Verify & Submit</button>
        </div>
    </div>
</form>

</main>
<!-- Popup Message Box -->
<div id="popup-box" class="popup-box" style="display: none;">
    <div class="popup-content">
        <p id="popup-message"></p>
        <button id="popup-ok-button" onclick="closePopup()">OK</button>
    </div>
</div>

<script src="../assets/js/captcha.js"></script>
<script>
    function showPopup(message, className = '') {
        const popupMessage = document.getElementById('popup-message');
        popupMessage.innerText = message;
        
        // Add a class to the message if provided
        if (className) {
            popupMessage.classList.add(className);
        }

        // Lock the page scroll position to prevent scrolling to the top
        document.body.style.overflow = 'hidden';

        document.getElementById('popup-box').style.display = 'block';
    }

    function closePopup() {
        document.body.style.overflow = 'auto';
        document.getElementById('popup-box').style.display = 'none';
    }

    function confirmDelete(button, event) {
        event.preventDefault();
    console.log('Confirm delete triggered');
    const confirmPopup = document.createElement('div');
    confirmPopup.classList.add('confirm-popup');
    confirmPopup.innerHTML = `
        <div class="confirm-message">Are you sure you want to delete this comment?</div>
        <button class="confirm-yes">Yes</button>
        <button class="confirm-no">No</button>
    `;

    document.body.appendChild(confirmPopup);
    console.log('Confirm popup added to DOM');

    // Handle Yes/No actions
    confirmPopup.querySelector('.confirm-yes').onclick = function () {
        console.log('Delete confirmed');
        button.parentElement.submit(); // Submit the delete form
    };
    confirmPopup.querySelector('.confirm-no').onclick = function () {
        console.log('Delete canceled');
        document.body.removeChild(confirmPopup); // Close the popup
    };
}
    // Function to generate a random color
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Apply random color to each comment author's name
    document.querySelectorAll('.comment-author').forEach(function(author) {
        author.style.color = getRandomColor();
    });
</script>
</body>
</html>
