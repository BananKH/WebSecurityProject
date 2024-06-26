<?php
// Start session
session_start();

// Include database connection
include("dbconnection.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Process form submission for posting
if (isset($_POST['submit_post'])) {
    // Sanitize user input
    $post_content = mysqli_real_escape_string($con, $_POST['post_content']);
    $username = $_SESSION['username'];

    // Insert post into database
    $insert_post_query = "INSERT INTO blog_posts (username, post_content) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $insert_post_query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $post_content);
    $execute_success = mysqli_stmt_execute($stmt);

    if (!$execute_success) {
        // Handle error if post insertion fails
        echo "Error: " . mysqli_error($con);
        exit();
    }

    // Redirect to blog page after posting
    header("Location: blog.php");
    exit();
}

// Retrieve blog posts from database
$get_posts_query = "SELECT username, post_content FROM blog_posts ORDER BY post_id DESC"; //post_id is the primary key
$result = mysqli_query($con, $get_posts_query);

// Check if query execution was successful
if (!$result) {
    // Handle error if query fails
    echo "Error: " . mysqli_error($con);
    exit();
}

// Fetch posts
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="css/form.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Blog</title>
</head>

<body>
    <div class="container">
        <!-- Form for posting a new blog post -->
        <form action="blog.php" method="POST">
            <h2>Welcome to the Blog!</h2>
            <p>Write your post:</p>
            <textarea id="post_content" name="post_content" rows="4" cols="25" maxlength="500" required></textarea><br>
            <div class="button">
                <input type="submit" name="submit_post" value="Post" />
            </div>
        </form>
        <hr>
        
        <!-- Display existing blog posts -->
        <?php foreach ($posts as $post): ?>
            <div>
            <!-- is used to encode HTML special characters  -->
                <h3><?php echo htmlspecialchars($post['username']); ?></h3>
                <p><?php echo htmlspecialchars($post['post_content']); ?></p>
            </div>
            <hr>
        <?php endforeach; ?>
        
        <!-- Logout button -->
        <form action="logout.php" method="post">
            <div class="button">
                <input type="submit" name="logout_btn" value="Logout">
            </div>
        </form>
    </div>
</body>
</html>
