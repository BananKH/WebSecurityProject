<?php
// Start session to store user data
session_start();

// Include database connection file
include("dbconnection.php");

// Check if the registration form is submitted
if (isset($_POST['register_btn'])) {
    // Retrieve user input data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $confirm_password = $_POST['confirm_password'];

    // Validate email
    if ($email === false) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: register.php");
        exit();
    }

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }

    // Prepare and bind parameters to check if the email already exists
    $check_email_query = "SELECT email FROM users WHERE email=?";
    if ($stmt = mysqli_prepare($con, $check_email_query)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $_SESSION['error'] = "Email already exists.";
            mysqli_stmt_close($stmt);
            header("Location: register.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database query error.";
        header("Location: register.php");
        exit();
    }

    // Hash the password using the default hashing algorithm
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind parameters to insert the new user into the database
    $query = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, '1')";
    if ($stmt = mysqli_prepare($con, $query)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);

        if (mysqli_stmt_execute($stmt)) {
            // Set session variables for the authenticated user
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username;
            // Registration Successful
            // Redirect the user to a new page to scan the QR code
            header("Location: scan_qr.php");
            exit();
        } else {
            // Log database error
            error_log("Database error: " . mysqli_stmt_error($stmt));
            $_SESSION['error'] = "Database error occurred. Please try again.";
            header("Location: register.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database query error.";
        header("Location: register.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="css/form.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
</head>
<body>
  <div class="container">
    <div class="title">Signup</div>
    <p>Create a new account</p>
    <div class="content">
      <form action="register.php" method="POST">
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <div class="user-details">
          <div class="input-box">
            <label>Username</label>
            <input type="text" placeholder="Enter your name" name="username" required/>
          </div>
          <div class="email">
            <label>Email</label>
            <input type="email" placeholder="Enter your email" name="email" required />
          </div>
          <div class="input-box">
            <label>Password</label>
            <input type="password" placeholder="Enter your password" name="password" required />
          </div>
          <div class="input-box">
            <label>Confirm Password</label>
            <input type="password" placeholder="Confirm your password" name="confirm_password" required />
          </div>
        </div>
        <div class="button">
          <input type="submit" name="register_btn" value="Submit" />
        </div>
      </form>
      <br>
      <div class="link">Already have an account? <a href="login.php">Login Now</a></div>
    </div>
  </div>
</body>
</html>
