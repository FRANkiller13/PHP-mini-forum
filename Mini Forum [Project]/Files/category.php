<?php
include 'db.php';

$category_id = $_GET['category_id'] ?? null;
if (!$category_id) {
    die("Category not specified!");
}

// Fetch sections for the category
$query = "SELECT id, name, description FROM sections WHERE category_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch category name
$query_category = "SELECT name FROM categories WHERE id = ?";
$stmt_category = $conn->prepare($query_category);
$stmt_category->bind_param("i", $category_id);
$stmt_category->execute();
$category = $stmt_category->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sections in <?php echo htmlspecialchars($category['name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>Sections in <?php echo htmlspecialchars($category['name']); ?></header>
    <div class="container">
        <h2>Sections</h2>
        <ul>
            <?php while ($section = $result->fetch_assoc()): ?>
                <li>
                    <a href="section.php?section_id=<?php echo $section['id']; ?>">
                        <?php echo htmlspecialchars($section['name']); ?>
                    </a> - <?php echo htmlspecialchars($section['description']); ?>
                </li>
            <?php endwhile; ?>
        </ul>

        <a href="new_post.php?category_id=<?php echo $category_id; ?>" class="button">Create New Post</a>
    </div>
</body>
</html>
