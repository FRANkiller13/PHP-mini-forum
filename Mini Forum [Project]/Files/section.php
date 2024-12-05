<?php
include 'db.php';

$section_id = $_GET['section_id'] ?? null;
if (!$section_id) {
    die("Section not specified!");
}

// Fetch posts for the section
$query = "SELECT p.id, p.title, u.username, p.created_at
          FROM posts p
          JOIN users3 u ON p.user_id = u.id
          WHERE p.section_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <link rel='stylesheet' href='css/styles.css'>
</head>
<body>
    <h1>Posts in Section</h1>
    <a href="new_post.php?section_id=<?php echo $section_id; ?>">Create New Post</a>
    <ul>
        <?php while ($post = $result->fetch_assoc()): ?>
            <li>
                <a href="post.php?post_id=<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
                by <?php echo htmlspecialchars($post['username']); ?>
                on <?php echo $post['created_at']; ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
