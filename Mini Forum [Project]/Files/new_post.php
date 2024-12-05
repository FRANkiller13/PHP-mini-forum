<?php
include 'db.php';

$category_id = $_GET['category_id'] ?? null;
if (!$category_id) {
    die("Category not specified!");
}

// Fetch category name to display in the form
$query = "SELECT name FROM categories WHERE id = ?";
$stmt = $conn->prepare($query);

// Check if prepare() failed
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = 1; // Hardcoded user ID for now. Replace with logged-in user ID

    // Insert new post into the database
    $query = "INSERT INTO posts (title, content, user_id, category_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Check if prepare() failed again
    if (!$stmt) {
        die("Error preparing insert query: " . $conn->error);
    }

    $stmt->bind_param("ssii", $title, $content, $user_id, $category_id);

    if ($stmt->execute()) {
        header("Location: category_posts.php?category_id=$category_id"); // Redirect back to category page after successful insert
        exit;
    } else {
        echo "Error: " . $stmt->error; // Show error if insert fails
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Post</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>Create New Post in <?php echo htmlspecialchars($category['name']); ?></header>
    <div class="container">
        <form method="POST">
            <label for="title">Post Title:</label><br>
            <input type="text" id="title" name="title" required><br>

            <label for="content">Post Content:</label><br>
            <textarea id="content" name="content" required></textarea><br>

            <button type="submit">Create Post</button>
        </form>
    </div>
</body>
</html>
