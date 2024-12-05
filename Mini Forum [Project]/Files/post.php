<?php
session_start();
include 'db.php';

$post_id = $_GET['post_id'] ?? null;
if (!$post_id) {
    die("Post not specified!");
}

// Fetch the post details
$query = "SELECT p.id, p.title, p.content, p.created_at, u.username FROM posts p 
          JOIN users3 u ON p.user_id = u.id
          WHERE p.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die("Post not found!");
}

// Fetch replies for this post
$query_replies = "SELECT r.id, r.content, r.created_at, u.username FROM replies r 
                  JOIN users3 u ON r.user_id = u.id
                  WHERE r.post_id = ? ORDER BY r.created_at ASC";
$stmt_replies = $conn->prepare($query_replies);
$stmt_replies->bind_param("i", $post_id);
$stmt_replies->execute();
$replies_result = $stmt_replies->get_result();

// Handle form submission for new reply
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to reply!"); // Prevent posting if not logged in
    }
    
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Get user_id from session

    // Insert new reply into the database
    $query_insert = "INSERT INTO replies (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param("iis", $post_id, $user_id, $content);

    if ($stmt_insert->execute()) {
        header("Location: post.php?post_id=$post_id"); // Reload the page after successful reply
        exit;
    } else {
        echo "Error: " . $stmt_insert->error; // Show error if insert fails
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header><?php echo htmlspecialchars($post['title']); ?></header>

    <div class="container">
        <div class="post">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p><small>Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></small></p>

        </div>

        <hr>

        <div class="replies">
            <h3>Replies:</h3>
            <?php if ($replies_result->num_rows > 0): ?>
                <ul>
                    <?php while ($reply = $replies_result->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($reply['username']); ?></strong> - 
                            <small>Posted on <?php echo date('F j, Y, g:i a', strtotime($reply['created_at'])); ?></small>
                            <p><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No replies yet.</p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Only show reply form if user is logged in -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="reply-form">
                <h3>Post a Reply</h3>
                <form method="POST">
                    <textarea name="content" rows="4" required></textarea><br>
                    <button type="submit">Submit Reply</button>
                </form>
            </div>
        <?php else: ?>
            <br>
            <p align="center">Please <a class="button2" href="login.php">login</a> to post a reply.</p>
        <?php endif; ?>
        <a class="button2" href="index.php">Go back</a>
    </div>

</body>
</html>
