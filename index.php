<?php
require_once 'config/db.php';
require_once 'includes/header.php';  // Include the header (unchanged)

// Fetch latest published posts and their authors from the database
$sql = "
    SELECT posts.*, users.username AS author_name
    FROM posts
    JOIN users ON posts.author_id = users.user_id
    WHERE posts.publish_status = 'published'
    ORDER BY posts.creation_date DESC
    LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Blog Posts</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>"> <!-- Link to your CSS -->
</head>
<body>
<main>
    <h1>Latest Blog Posts</h1>

    <!-- Grid Container for Posts -->
    <div class="posts-grid">
        <!-- Check if there are any posts -->
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <a href="posts/view.php?id=<?php echo $post['post_id']; ?>" class="post">
                    <!-- Overlay -->
                    <div class="overlay"></div>

                    <!-- Display the image if available -->
                    <?php if (!empty($post['image_path'])): ?>
                        <img class="post-img" src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image">
                    <?php endif; ?>

                    <!-- Post Title -->
                    <h2><?php echo htmlspecialchars($post['title']); ?></h2>

                    <!-- Post Excerpt or Summary -->
                    <p><?php echo nl2br(htmlspecialchars(substr($post['summary'], 0, 100))); ?>...</p>

                    <!-- Creation Date and Author Name -->
                    <div class="author-info">
                        <p><em>Posted on <?php echo date("F j, Y", strtotime($post['creation_date'])); ?></em></p>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($post['author_name']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Message if no posts are available -->
            <p>No blog posts available. Check back later!</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?> <!-- Include the footer -->
</body>
</html>
