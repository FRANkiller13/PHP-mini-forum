<?php
session_start();
include 'db.php';

// Fetch all categories
$query = "SELECT id, name, description FROM categories";
$result = $conn->query($query);

// Fetch posts for each category, limit to 5 posts per category
$categories_with_posts = [];

while ($category = $result->fetch_assoc()) {
    $category_id = $category['id'];
    
    // Fetch only 5 posts within this category
    $query_posts = "SELECT p.id, p.title, p.created_at, u.username FROM posts p 
                    JOIN users3 u ON p.user_id = u.id
                    WHERE p.category_id = ? ORDER BY p.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($query_posts);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $posts_result = $stmt->get_result();

    // Store category and its posts
    $categories_with_posts[] = [
        'category' => $category,
        'posts' => $posts_result
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>Mini Forum</header>
    
    <div class="container">
        <?php if (isset($_SESSION['username'])): ?>
            <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! - <a class="button2" href="logout.php">Logout</a></p>
        <?php else: ?>
            <p class="welcome-message">Please <a class="button2" href="login.php">login</a> to join the discussion.</p>
        <?php endif; ?>

        <?php foreach ($categories_with_posts as $category_with_posts): ?>
            <div class="category">
                <h2><?php echo htmlspecialchars($category_with_posts['category']['name']); ?></h2>
                <p style="font-size: 12px;"><i><?php echo htmlspecialchars($category_with_posts['category']['description']); ?></i></p>

                <!-- Display posts in this category -->
                <ul>
                    <?php while ($post = $category_with_posts['posts']->fetch_assoc()): ?>
                        <li>
                            <a href="post.php?post_id=<?php echo $post['id']; ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                            <small>Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <br>
                
                <!-- Show link to view all posts in the category -->
                <a href="category_posts.php?category_id=<?php echo $category_with_posts['category']['id']; ?>" class="button">View All Posts</a>
                
                <!-- Only show 'Create New Post' link if user is logged in -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="new_post.php?category_id=<?php echo $category_with_posts['category']['id']; ?>" class="button">Create New Post</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <p align="center">Copyright &copy; 2024 - Created by <a href="https://fran.restream.gr/" target="_blank">FRANkiller13</a></p>
</body>
</html>
