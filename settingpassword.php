<?php
// Start session to manage user authentication
session_start();
include ("dbconnection.php");

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    $_SESSION["token"] = $token;
}


if (isset($_POST['email']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    if (isset($_POST['update_pass_btn'])) {

        $email = $_POST["email"];
        $password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        $token = $_SESSION["token"];

        // Checking token is valid or not
        $check_token = "SELECT * FROM user WHERE email='$email' and token='$token' and status=1";
        $check_token_run = mysqli_query($con, $check_token);

        if (mysqli_num_rows($check_token_run) > 0) {
            if ($password == $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_password = "UPDATE user SET password='$hashed_password' WHERE email='$email'";
                $update_password_run = mysqli_query($con, $update_password);

                $update_token = "UPDATE user SET status=2 WHERE email='$email'";
                $update_token_run = mysqli_query($con, $update_token);

                if ($update_password_run) {
                    
                    $_SESSION['status'] = "New password successfully updated.";
                    header("Location: login.php");
                    exit(0); // Terminate script after success message
                } else {
                    echo "<script>alert('Didn\'t update password. Something went wrong.');</script>";
                    exit(0); // Terminate script after error message
                }
            } else {
                echo "<script>alert('Password and Confirm password don\'t match.'); window.location.replace='settingpassword.php';</script>";
                exit(0);
            }
        } else {
            echo "<script>alert('Invalid Token.'); window.location.replace='settingpassword.php';</script>";
            exit(0);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="css/form.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>
    <div class="container">
        <form action="settingpassword.php" method="POST">
            <div class=" title">Change Password
            </div>
            <div class="user-details">
                <div class="email">
                    <label>Email</label>
                    <input type="text" placeholder="Enter your email" name="email" required />
                </div>
                <div class="input-box">
                    <label>New Password</label>
                    <input type="password" placeholder="Enter new password" name="new_password" required />
                </div>
                <div class="input-box">
                    <label>Confirm Password</label>
                    <input type="password" placeholder="Confirm new password" name="confirm_password" required />
                </div>
            </div>
            <div class="button">
                <input type="submit" name="update_pass_btn" value="Update Password" />
            </div>
        </form>
    </div>
</body>