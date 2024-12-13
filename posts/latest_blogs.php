<?php
session_start();
require_once '../config/db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Handle sorting and filtering
$sort_order = $_GET['sort'] ?? 'latest';
$filter_tag = $_GET['tag'] ?? null;
$filter_category = $_GET['category'] ?? null;

// Handle search
$search_query = $_GET['search'] ?? null;

// Base query for fetching posts
$sql = "
    SELECT posts.*, users.username AS author_name
    FROM posts
    JOIN users ON posts.author_id = users.user_id
    WHERE posts.publish_status = 'published'";

// Add search condition
if ($search_query) {
    $sql .= " AND posts.title LIKE ?";
}

// Add filtering by tag
if ($filter_tag) {
    $sql .= "
    AND posts.post_id IN (
        SELECT post_id FROM post_tags
        JOIN tags ON post_tags.tag_id = tags.tag_id
        WHERE tags.tag_name = ?
    )";
}

// Add filtering by category
if ($filter_category) {
    $sql .= "
    AND posts.post_id IN (
        SELECT post_id FROM post_categories
        JOIN categories ON post_categories.category_id = categories.category_id
        WHERE categories.category_name = ?
    )";
}

// Add sorting
switch ($sort_order) {
    case 'oldest':
        $sql .= " ORDER BY posts.creation_date ASC";
        break;
    case 'ascending':
        $sql .= " ORDER BY posts.title ASC";
        break;
    case 'descending':
        $sql .= " ORDER BY posts.title DESC";
        break;
    default: // Latest
        $sql .= " ORDER BY posts.creation_date DESC";
        break;
}

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$params = [];
if ($search_query) $params[] = '%' . $search_query . '%';
if ($filter_tag) $params[] = $filter_tag;
if ($filter_category) $params[] = $filter_category;
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch tags and categories
$tags = $pdo->query("SELECT tag_name FROM tags")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Latest Blog Posts</title>
    <link rel="stylesheet" href="../assets/css/latest_blogs.css?v=<?php echo time(); ?>">
</head>
<body>
<main>
    <!-- Logout Button -->
    <form method="GET" style="text-align: right;">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>

    <h1>Latest Blog Posts</h1>

    <div class="search-container">
    <form method="GET">
        <label for="search">Search Blogs:</label>
        <input type="text" id="search" name="search" class="search-input" 
               value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter blog title...">
        <button type="submit" class="search-btn">Search</button>
        <button type="button" class="reset-btn" onclick="window.location.href='?';">Reset</button>
    </form>
</div>


    <!-- Filter and Sort Options -->
    <div class="filter-container">
        <!-- Sorting -->
        <form method="GET">
            <label for="sort">Sort By:</label>
            <select name="sort" id="sort" class="filter-select" onchange="this.form.submit()">
                <option value="latest" <?php if ($sort_order === 'latest') echo 'selected'; ?>>Latest</option>
                <option value="oldest" <?php if ($sort_order === 'oldest') echo 'selected'; ?>>Oldest</option>
                <option value="ascending" <?php if ($sort_order === 'ascending') echo 'selected'; ?>>Title (A-Z)</option>
                <option value="descending" <?php if ($sort_order === 'descending') echo 'selected'; ?>>Title (Z-A)</option>
            </select>
        </form>

        <!-- Tags Filter -->
        <form method="GET">
            <label for="tag">Filter by Tag:</label>
            <select name="tag" id="tag" class="filter-select" onchange="this.form.submit()">
                <option value="">All Tags</option>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?php echo htmlspecialchars($tag['tag_name']); ?>" 
                        <?php if ($filter_tag === $tag['tag_name']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($tag['tag_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Categories Filter -->
        <form method="GET">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['category_name']); ?>" 
                        <?php if ($filter_category === $category['category_name']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Grid Container for Posts -->
    <div class="posts-grid">
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <a href="view.php?id=<?php echo $post['post_id']; ?>" class="post">
                <?php if (!empty($post['image_path'])): ?>
                    <img src="../<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image">
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars(substr($post['summary'], 0, 100))); ?>...</p>
                <p><em>Posted on <?php echo date("F j, Y", strtotime($post['creation_date'])); ?></em></p>
                <p><strong>Author:</strong> <?php echo htmlspecialchars($post['author_name']); ?></p>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No blog posts available. Check back later!</p>
    <?php endif; ?>
</div>

</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>
