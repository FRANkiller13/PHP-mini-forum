<?php
session_start();
include 'db.php';

$category_id = $_GET['category_id'] ?? null;
if (!$category_id) {
    die("Category not specified!");
}

// Fetch category details
$query = "SELECT id, name, description FROM categories WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    die("Category not found!");
}

// Fetch all posts for this category
$query_posts = "SELECT p.id, p.title, p.created_at, u.username FROM posts p 
                JOIN users3 u ON p.user_id = u.id
                WHERE p.category_id = ? ORDER BY p.created_at DESC";
$stmt_posts = $conn->prepare($query_posts);
$stmt_posts->bind_param("i", $category_id);
$stmt_posts->execute();
$posts_result = $stmt_posts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts in <?php echo htmlspecialchars($category['name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>All Posts in <?php echo htmlspecialchars($category['name']); ?></header>

    <div class="container">
        <p><a href="index.php" class="button">Back to Categories</a></p>


        <ul>
            <?php while ($post = $posts_result->fetch_assoc()): ?>
                <li>
                <a href="post.php?post_id=<?php echo $post['id']; ?>">
                                <font color="#0056b3">
                                <?php echo htmlspecialchars($post['title']); ?>
                                </font>
                            </a>
                    <small>Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></small>
                </li>
            <?php endwhile; ?>
        </ul>

        <?php if ($posts_result->num_rows == 0): ?>
            <p>No posts available in this category.</p>
        <?php endif; ?>
    </div>
</body>
</html>
