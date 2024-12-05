<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];

    // Insert new category into the database
    $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $category_name, $category_description);
    
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect to the homepage after successful insertion
        exit;
    } else {
        echo "Error: " . $stmt->error; // Show error if insert fails
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Category</title>
    <link rel='stylesheet' href='css/styles.css'>
</head>
<body>
    <header>Create New Category</header>
    <div class="container">
        <form method="POST">
            <label for="category_name">Category Name:</label><br>
            <input type="text" id="category_name" name="category_name" required><br>
            
            <label for="category_description">Category Description:</label><br>
            <textarea id="category_description" name="category_description" required></textarea><br>
            
            <button type="submit">Create Category</button>
        </form>
    </div>
</body>
</html>
