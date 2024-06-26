<?php
session_start();
include("dbconnection.php"); // Include your database connection file

// Function to generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate CSRF token by using hash equals to prevent timing attack.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF detected. Request blocked.");
    }

    // Process change email request
    $new_email = $_POST['new_email'];
    $username = $_SESSION['username']; // Assuming you have stored the username in session after login

    // Update user's email in the database
    $sql = "UPDATE user SET email = ? WHERE username = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        die("Error in preparing SQL statement: " . $con->error);
    }

    $stmt->bind_param("ss", $new_email, $username);

    // Execute the prepared statement
    if ($stmt->execute()) {
        $success_message = "Email address changed to: " . $new_email;
    } else {
        $error_message = "Error updating email address: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Change Email</title>
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <div class="container">
        <h2>Change Your Email</h2>

        <div class="user-details">
            <form action="changeemail.php" method="POST">
                <!-- CSRF Token -->
                <input type="text" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- New Email Address -->
                <div class="email">
                    <label>New Email</label>
                    <input type="email" placeholder="Enter your new email" name="new_email" required />
                </div>
        </div>
        <!-- Submit Button -->
        <div class="button">
            <input type="submit" name="ChangeEmail" value="Change Email" />
        </div>
        </form>
        <!-- Display change email result -->
        <?php if (isset($success_message)) : ?>
            <p><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)) : ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        <br>
        <!-- Logout form -->
        <form action="logout.php" method="post">
            <div class="button">
                <input type="submit" name="logout_btn" value="Logout">
            </div>
        </form>
    </div>
</body>

</html>
