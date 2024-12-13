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

// Ensure the user is logged in as an author
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header("Location: ../login.php");
    exit();
}

$author_id = $_SESSION['user_id']; // Fetch the logged-in author's ID
$username = $_SESSION['username']; // Assume the username is stored in session

if (isset($_SESSION['popup_message'])) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() {
        showPopup('" . htmlspecialchars($_SESSION['popup_message'], ENT_QUOTES, 'UTF-8') . "');
    });</script>";    
    unset($_SESSION['popup_message']); // Clear the message after displaying
}
?>

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

    function getGreeting() {
        const hours = new Date().getHours();
        let greeting = "Good Evening";
        if (hours < 12) greeting = "Good Morning";
        else if (hours < 18) greeting = "Good Afternoon";
        document.getElementById("greeting").innerText = greeting;
    }
    window.onload = getGreeting; // Call this function on page load
</script>

<link rel="stylesheet" href="../assets/css/create.css?v=<?php echo time(); ?>">

<!-- Top Navbar with Greeting -->
<div class="navbar">
    <div class="greeting">
        <span id="greeting"></span>, <?php echo htmlspecialchars($username); ?>
    </div>
    <div class="nav-buttons">
        <a href="?logout=true" class="logout-btn">Logout</a>
        <form action="latest_blogs.php" method="GET" class="latest-btn-form">
            <button type="submit" class="latest-btn">Latest Blogs</button>
        </form>
    </div>
</div>


<?php

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create a new post
    if (isset($_POST['create'])) {
        $title = $_POST['title'];
        $body_text = $_POST['body_text'];
        $summary = $_POST['summary'];
        $publish_status = $_POST['publish_status'];
        $category = $_POST['categories'];  // New category field
        $tags = $_POST['tags'];  // Tags field
        $creation_date = date('Y-m-d H:i:s');
        $image_path = null; // Default to null if no image is uploaded

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

            // Validate the image file (allowed extensions: jpg, jpeg, png, gif)
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($image_ext), $allowed_extensions)) {
                $image_new_name = uniqid('post_', true) . '.' . $image_ext;
                $image_path = 'uploads/' . $image_new_name; // Relative path

                // Ensure the uploads folder exists
                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0777, true); // Create the folder if it doesn't exist
                }

                // Move the uploaded image to the uploads folder
                if (!move_uploaded_file($image_tmp_name, '../' . $image_path)) {
                    $image_path = null; // Reset if the upload fails
                    $_SESSION['popup_message'] = 'Failed to upload the Image!';
                }
            } else {
                $_SESSION['popup_message'] = 'Invalid image file type. Only JPG, PNG, and GIF are allowed.';
            }
        }

        // Insert the new post into the database
        try {
            $sql = "INSERT INTO posts (title, body_text, summary, author_id, creation_date, publish_status, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $body_text, $summary, $author_id, $creation_date, $publish_status, $image_path]);
            $post_id = $pdo->lastInsertId(); // Get the ID of the newly created post
        
            // Insert categories into the post_categories table
            if (!empty($category)) {
                // Insert or find the category ID
                $sql_category = "INSERT IGNORE INTO categories (category_name) VALUES (?)";
                $stmt_category = $pdo->prepare($sql_category);
                $stmt_category->execute([$category]);
            
                $sql_get_category_id = "SELECT category_id FROM categories WHERE category_name = ?";
                $stmt_get_category_id = $pdo->prepare($sql_get_category_id);
                $stmt_get_category_id->execute([$category]);
                $category_id = $stmt_get_category_id->fetchColumn();
            
                // Link post to category
                $sql_post_category = "INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)";
                $stmt_post_category = $pdo->prepare($sql_post_category);
                $stmt_post_category->execute([$post_id, $category_id]);
            }            
        
            // Insert tags into the post_tags table
            if (!empty($_POST['tags'])) {
                $tags = array_map('trim', explode(',', $_POST['tags'])); // Trim each tag after splitting
                foreach ($tags as $tag_name) {
                    // Insert or find the tag ID
                    $sql_tag = "INSERT IGNORE INTO tags (tag_name) VALUES (?)";
                    $stmt_tag = $pdo->prepare($sql_tag);
                    $stmt_tag->execute([$tag_name]);
        
                    $sql_get_tag_id = "SELECT tag_id FROM tags WHERE tag_name = ?";
                    $stmt_get_tag_id = $pdo->prepare($sql_get_tag_id);
                    $stmt_get_tag_id->execute([$tag_name]);
                    $tag_id = $stmt_get_tag_id->fetchColumn();
        
                    // Link post to tag
                    $sql_post_tag = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
                    $stmt_post_tag = $pdo->prepare($sql_post_tag);
                    $stmt_post_tag->execute([$post_id, $tag_id]);
                }
            }
            $_SESSION['popup_message'] = 'Post created successfully!';
        } catch (PDOException $e) {
            $_SESSION['popup_message'] = 'Error: ' . addslashes($e->getMessage());
        }
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $post_id);
        exit();
        
    }
    // Update an existing post
    if (isset($_POST['update'])) {
        $post_id = $_POST['post_id'];
        $title = $_POST['title'];
        $body_text = $_POST['body_text'];
        $summary = $_POST['summary'];
        $publish_status = $_POST['publish_status'];
        $category = $_POST['categories'];  // Updated category
        $tags = $_POST['tags'];  // Updated tags
        $update_date = date('Y-m-d H:i:s');
        $image_path = $_POST['existing_image']; // Use the existing image if no new one is uploaded

        // Handle image upload if a new image is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

            // Validate the image file (allowed extensions: jpg, jpeg, png, gif)
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($image_ext), $allowed_extensions)) {
                $image_new_name = uniqid('post_', true) . '.' . $image_ext;
                $image_path = 'uploads/' . $image_new_name;

                // Ensure the uploads folder exists
                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0777, true);
                }

                // Move the uploaded image to the uploads folder
                if (!move_uploaded_file($image_tmp_name, '../' . $image_path)) {
                    $image_path = $_POST['existing_image']; // Fallback to the old image if upload fails
                    $_SESSION['popup_message'] = 'Failed to upload the Image!';
                }
            } else {
                $_SESSION['popup_message'] = 'Invalid image file type. Only JPG, PNG, and GIF are allowed.';
            }
        }

        // Update the post in the database
        try {
            $sql = "UPDATE posts SET title = ?, body_text = ?, summary = ?, publish_status = ?, update_date = ?, image_path = ? 
                    WHERE post_id = ? AND author_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $body_text, $summary, $publish_status, $update_date, $image_path, $post_id, $author_id]);

            // Update categories
    $sql_delete_categories = "DELETE FROM post_categories WHERE post_id = ?";
    $stmt_delete_categories = $pdo->prepare($sql_delete_categories);
    $stmt_delete_categories->execute([$post_id]);

    if (!empty($category)) {
        // Check if the category exists
        $sql_get_category_id = "SELECT category_id FROM categories WHERE category_name = ?";
        $stmt_get_category_id = $pdo->prepare($sql_get_category_id);
        $stmt_get_category_id->execute([$category]);
        $category_id = $stmt_get_category_id->fetchColumn();

        // If category doesn't exist, insert it
        if (!$category_id) {
            $sql_category = "INSERT INTO categories (category_name) VALUES (?)";
            $stmt_category = $pdo->prepare($sql_category);
            $stmt_category->execute([$category]);

            // Get the newly inserted category_id
            $category_id = $pdo->lastInsertId();
        }

        // Link the post to the category
        $sql_post_category = "INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)";
        $stmt_post_category = $pdo->prepare($sql_post_category);
        $stmt_post_category->execute([$post_id, $category_id]);
    }

    // Update tags
    if (!empty($tags)) {
        $tags = array_map('trim', explode(',', $tags)); // Convert string into an array and trim whitespace
        $sql_delete_tags = "DELETE FROM post_tags WHERE post_id = ?";
        $stmt_delete_tags = $pdo->prepare($sql_delete_tags);
        $stmt_delete_tags->execute([$post_id]);

    foreach ($tags as $tag_name) {
        // Check if the tag exists
        $sql_get_tag_id = "SELECT tag_id FROM tags WHERE tag_name = ?";
        $stmt_get_tag_id = $pdo->prepare($sql_get_tag_id);
        $stmt_get_tag_id->execute([$tag_name]);
        $tag_id = $stmt_get_tag_id->fetchColumn();

        // If the tag doesn't exist, insert it
        if (!$tag_id) {
            $sql_tag = "INSERT INTO tags (tag_name) VALUES (?)";
            $stmt_tag = $pdo->prepare($sql_tag);
            $stmt_tag->execute([$tag_name]);

            // Get the newly inserted tag_id
            $tag_id = $pdo->lastInsertId();
        }

        // Link the post to the tag
        $sql_post_tag = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
        $stmt_post_tag = $pdo->prepare($sql_post_tag);
        $stmt_post_tag->execute([$post_id, $tag_id]);
    }
}

            $_SESSION['popup_message'] = 'Post updated successfully!';
        } catch (PDOException $e) {
            $_SESSION['popup_message'] = 'Error: ' . addslashes($e->getMessage());
        }
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $post_id);
        exit();
    }

    // Delete a post
    if (isset($_POST['delete'])) {
        $post_id = $_POST['post_id'];

        // First, delete all comments associated with the post
        $sql_delete_comments = "DELETE FROM comments WHERE post_id = ?";
        $stmt_delete_comments = $pdo->prepare($sql_delete_comments);
        $stmt_delete_comments->execute([$post_id]);

        // Delete the post's tags
        $sql_delete_tags = "DELETE FROM post_tags WHERE post_id = ?";
        $stmt_delete_tags = $pdo->prepare($sql_delete_tags);
        $stmt_delete_tags->execute([$post_id]);

        // Delete the post's categories
        $sql_delete_categories = "DELETE FROM post_categories WHERE post_id = ?";
        $stmt_delete_categories = $pdo->prepare($sql_delete_categories);
        $stmt_delete_categories->execute([$post_id]);

        // Now, delete orphan tags that are no longer associated with any posts
        $sql_delete_orphan_tags = "DELETE FROM tags WHERE tag_id NOT IN (SELECT DISTINCT tag_id FROM post_tags)";
        $stmt_delete_orphan_tags = $pdo->prepare($sql_delete_orphan_tags);
        $stmt_delete_orphan_tags->execute();

        // Now, delete orphan categories that are no longer associated with any posts
        $sql_delete_orphan_categories = "DELETE FROM categories WHERE category_id NOT IN (SELECT DISTINCT category_id FROM post_categories)";
        $stmt_delete_orphan_categories = $pdo->prepare($sql_delete_orphan_categories);
        $stmt_delete_orphan_categories->execute();

        // Then, delete the post's image file if it exists
        $sql_image = "SELECT image_path FROM posts WHERE post_id = ? AND author_id = ?";
        $stmt_image = $pdo->prepare($sql_image);
        $stmt_image->execute([$post_id, $author_id]);
        $image = $stmt_image->fetch(PDO::FETCH_ASSOC);

        if ($image && $image['image_path'] && file_exists('../' . $image['image_path'])) {
            unlink('../' . $image['image_path']); // Delete the image file
        }

        // Delete the post from the database
        $sql_delete_post = "DELETE FROM posts WHERE post_id = ? AND author_id = ?";
        $stmt_delete_post = $pdo->prepare($sql_delete_post);
        $stmt_delete_post->execute([$post_id, $author_id]);
        
        $_SESSION['popup_message'] = 'Post deleted successfully!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

}

// Fetch all posts by the author
$sql = "SELECT * FROM posts WHERE author_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$author_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Include CKEditor script -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<style>
        .cke_notifications_area { display: none; }
    </style>

<h1>Manage Posts</h1>

<!-- Form to create a new post -->
<form method="POST" enctype="multipart/form-data">
    <h3>Create New Post</h3>
    <label for="title">Title:</label>
    <input type="text" name="title" placeholder="Title" required><br>
    <label for="body_text">Body:</label>
    <textarea id="body_text" name="body_text" placeholder="Body" required></textarea><br>
    <script>
  // Initialize CKEditor for the textarea with name="body_text"
  CKEDITOR.replace('body_text', {
    height: 300,
    toolbar: [
        { name: 'clipboard', items: ['Undo', 'Redo'] },
        { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
        { name: 'links', items: ['Link', 'Unlink'] },
        { name: 'insert', items: ['Table'] },
        { name: 'tools', items: ['Maximize'] },
        { name: 'editing', items: ['Scayt'] }
    ],
    removePlugins: 'elementspath',
    removePlugins: 'about',
    resize_enabled: false,
    disallowedContent: '*[*]{*}',
});
</script>
    <label for="summary">Summary:</label>
    <textarea id="summary" name="summary" placeholder="Summary" required></textarea><br>

    <!-- Tags Field -->
    <label for="tags">Tags:</label>
    <input type="text" name="tags" placeholder="Enter tags (comma separated)" style="width: 300px;"><br><br>

    <?php
// Initialize category variable to prevent "undefined variable" warning
$category = isset($_POST['categories']) ? $_POST['categories'] : '';
?>

<!-- Categories Dropdown -->
<label for="categories">Category:</label>
<select name="categories" required>
    <option value="business_marketing" <?php if ($category == 'business_marketing') echo 'selected'; ?>>Business marketing</option>
    <option value="personal_development" <?php if ($category == 'personal_development') echo 'selected'; ?>>Personal development</option>
    <option value="lifestyle" <?php if ($category == 'lifestyle') echo 'selected'; ?>>Lifestyle</option>
    <option value="news_blogs" <?php if ($category == 'news_blogs') echo 'selected'; ?>>News blogs</option>
    <option value="travel" <?php if ($category == 'travel') echo 'selected'; ?>>Travel</option>
    <option value="affiliate_blogs" <?php if ($category == 'affiliate_blogs') echo 'selected'; ?>>Affiliate blogs</option>
    <option value="food_blogs" <?php if ($category == 'food_blogs') echo 'selected'; ?>>Food blogs</option>
    <option value="diy" <?php if ($category == 'diy') echo 'selected'; ?>>DIY</option>
    <option value="niche_blogs" <?php if ($category == 'niche_blogs') echo 'selected'; ?>>Niche blogs</option>
    <option value="music" <?php if ($category == 'music') echo 'selected'; ?>>Music</option>
    <option value="parenting_blogs" <?php if ($category == 'parenting_blogs') echo 'selected'; ?>>Parenting blogs</option>
    <option value="politics" <?php if ($category == 'politics') echo 'selected'; ?>>Politics</option>
    <option value="sports" <?php if ($category == 'sports') echo 'selected'; ?>>Sports</option>
    <option value="fashion_blogs" <?php if ($category == 'fashion_blogs') echo 'selected'; ?>>Fashion blogs</option>
    <option value="health_and_fitness_blogs" <?php if ($category == 'health_and_fitness_blogs') echo 'selected'; ?>>Health and fitness blogs</option>
    <option value="finance_blog" <?php if ($category == 'finance_blog') echo 'selected'; ?>>Finance blog</option>
    <option value="movie_blogs" <?php if ($category == 'movie_blogs') echo 'selected'; ?>>Movie blogs</option>
    <option value="multimedia_blogs" <?php if ($category == 'multimedia_blogs') echo 'selected'; ?>>Multimedia blogs</option>
    <option value="religion_blogs" <?php if ($category == 'religion_blogs') echo 'selected'; ?>>Religion blogs</option>
    <option value="interior_design" <?php if ($category == 'interior_design') echo 'selected'; ?>>Interior design</option>
    <option value="news" <?php if ($category == 'news') echo 'selected'; ?>>News</option>
    <option value="fashion_and_beauty_blogs" <?php if ($category == 'fashion_and_beauty_blogs') echo 'selected'; ?>>Fashion and beauty blogs</option>
    <option value="customize_your_blog" <?php if ($category == 'customize_your_blog') echo 'selected'; ?>>Customize your blog</option>
    <option value="art_and_design_blogs" <?php if ($category == 'art_and_design_blogs') echo 'selected'; ?>>Art and design blogs</option>
</select><br><br>


    <!-- Publish Status -->
    <label for="publish_status">Publish Status:</label>
    <select name="publish_status">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
    </select><br><br>

    <!-- Image Upload -->
    <label for="image">Upload Image (Optional):</label>
    <input type="file" name="image"><br><br>

    <input type="submit" name="create" value="Create Post">
</form>


<!-- List of posts -->
<h3>Your Posts</h3>
<div class="post-list">
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h4><?php echo htmlspecialchars($post['title']); ?></h4>
            <h5>Uploaded Image:</h5>
            <?php if (!empty($post['image_path'])): ?>
                <img src="../<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image" width="100"><br>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($post['image_path']); ?>">

                <!-- Title Field -->
                <label for="title">Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>

                <!-- Body Text Field -->
                <label for="body_text">Body:</label>
                <textarea id="body_text_<?php echo $post['post_id']; ?>" name="body_text"><?php echo htmlspecialchars($post['body_text']); ?></textarea><br>

                <!-- CKEditor Initialization -->
                <script>
                    CKEDITOR.replace('body_text_<?php echo $post['post_id']; ?>', {
                        height: 300,
                        toolbar: [
                            { name: 'clipboard', items: ['Undo', 'Redo'] },
                            { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                            { name: 'links', items: ['Link', 'Unlink'] },
                            { name: 'insert', items: ['Table'] },
                            { name: 'tools', items: ['Maximize'] },
                            { name: 'editing', items: ['Scayt'] }
                        ],
                        removePlugins: 'elementspath,about',
                        resize_enabled: false,
                        disallowedContent: '*[*]{*}',
                    });
                </script>

                <!-- Summary Field -->
                <label for="summary">Summary:</label>
                <textarea name="summary"><?php echo htmlspecialchars($post['summary']); ?></textarea><br>

            <!-- Categories Dropdown (Pre-populate selected category) -->
<label for="categories">Category:</label>
<select name="categories" required>
    <?php
    // Fetch the current category for this post
    $sql_get_category = "SELECT category_name FROM categories 
                         JOIN post_categories ON categories.category_id = post_categories.category_id 
                         WHERE post_categories.post_id = ?";
    $stmt_get_category = $pdo->prepare($sql_get_category);
    $stmt_get_category->execute([$post['post_id']]);
    $current_category = $stmt_get_category->fetchColumn();
    ?>
    <option value="business_marketing" <?php if ($current_category == 'business_marketing') echo 'selected'; ?>>Business marketing</option>
    <option value="personal_development" <?php if ($current_category == 'personal_development') echo 'selected'; ?>>Personal development</option>
    <option value="lifestyle" <?php if ($current_category == 'lifestyle') echo 'selected'; ?>>Lifestyle</option>
    <option value="news_blogs" <?php if ($current_category == 'news_blogs') echo 'selected'; ?>>News blogs</option>
    <option value="travel" <?php if ($current_category == 'travel') echo 'selected'; ?>>Travel</option>
    <option value="affiliate_blogs" <?php if ($current_category == 'affiliate_blogs') echo 'selected'; ?>>Affiliate blogs</option>
    <option value="food_blogs" <?php if ($current_category == 'food_blogs') echo 'selected'; ?>>Food blogs</option>
    <option value="diy" <?php if ($current_category == 'diy') echo 'selected'; ?>>DIY</option>
    <option value="niche_blogs" <?php if ($current_category == 'niche_blogs') echo 'selected'; ?>>Niche blogs</option>
    <option value="music" <?php if ($current_category == 'music') echo 'selected'; ?>>Music</option>
    <option value="parenting_blogs" <?php if ($current_category == 'parenting_blogs') echo 'selected'; ?>>Parenting blogs</option>
    <option value="politics" <?php if ($current_category == 'politics') echo 'selected'; ?>>Politics</option>
    <option value="sports" <?php if ($current_category == 'sports') echo 'selected'; ?>>Sports</option>
    <option value="fashion_blogs" <?php if ($current_category == 'fashion_blogs') echo 'selected'; ?>>Fashion blogs</option>
    <option value="health_and_fitness_blogs" <?php if ($current_category == 'health_and_fitness_blogs') echo 'selected'; ?>>Health and fitness blogs</option>
    <option value="finance_blog" <?php if ($current_category == 'finance_blog') echo 'selected'; ?>>Finance blog</option>
    <option value="movie_blogs" <?php if ($current_category == 'movie_blogs') echo 'selected'; ?>>Movie blogs</option>
    <option value="multimedia_blogs" <?php if ($current_category == 'multimedia_blogs') echo 'selected'; ?>>Multimedia blogs</option>
    <option value="religion_blogs" <?php if ($current_category == 'religion_blogs') echo 'selected'; ?>>Religion blogs</option>
    <option value="interior_design" <?php if ($current_category == 'interior_design') echo 'selected'; ?>>Interior design</option>
    <option value="news" <?php if ($current_category == 'news') echo 'selected'; ?>>News</option>
    <option value="fashion_and_beauty_blogs" <?php if ($current_category == 'fashion_and_beauty_blogs') echo 'selected'; ?>>Fashion and beauty blogs</option>
    <option value="customize_your_blog" <?php if ($current_category == 'customize_your_blog') echo 'selected'; ?>>Customize your blog</option>
    <option value="art_and_design_blogs" <?php if ($current_category == 'art_and_design_blogs') echo 'selected'; ?>>Art and design blogs</option>
    <!-- Add more categories here -->
</select><br><br>

<!-- Tags Field (Pre-populate existing tags) -->
<label for="tags">Tags:</label>
<input type="text" name="tags" value="<?php
// Fetch the current tags for this post
$sql_get_tags = "SELECT tag_name FROM tags 
                 JOIN post_tags ON tags.tag_id = post_tags.tag_id 
                 WHERE post_tags.post_id = ?";
$stmt_get_tags = $pdo->prepare($sql_get_tags);
$stmt_get_tags->execute([$post['post_id']]);
$tags = $stmt_get_tags->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $tags);
?>" style="width: 300px;"><br><br>


            <select name="publish_status">
                <option value="draft" <?php if ($post['publish_status'] == 'draft') echo 'selected'; ?>>Draft</option>
                <option value="published" <?php if ($post['publish_status'] == 'published') echo 'selected'; ?>>Published</option>
            </select><br>
            <input type="file" name="image" accept="image/*"><br>
            <button type="submit" name="update" class="update-btn">Update</button>
            <button type="submit" name="delete" class="delete-btn">Delete</button>
        </form>
        </div>
    <?php endforeach; ?>
</div>

<!-- Popup Message Box -->
<div id="popup-box" class="popup-box" style="display: none;">
    <div class="popup-content">
        <p id="popup-message"></p>
        <button id="popup-ok-button" onclick="closePopup()">OK</button>
    </div>
</div>