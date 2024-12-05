<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch user data by username
    $query = "SELECT id, username, password FROM users3 WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Check if user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Start the session
        session_start();

        // Store user data in session
        $_SESSION['user_id'] = $user['id'];     // Store user ID
        $_SESSION['username'] = $user['username'];  // Store username

        // Redirect to the home page
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>Login</header>
    <div class="container">
        <form method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Login</button>
        </form>
        <p align="center">if you dont have account register <a href="register.php" class="button2">here</a></p>
    </div>
    <p align="center">Copyright &copy; 2024 - Created by <a href="https://fran.restream.gr/" target="_blank">FRANkiller13</a></p>
</body>
</html>
