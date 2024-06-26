<?php
session_start();
include("dbconnection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_password_reset($get_name, $get_email, $token) {
    $mail = new PHPMailer(true);

    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'fortestweb@outlook.com';
    $mail->Password = 'websecurity2024';
    $mail->SMTPSecure = 'tls';

    // Sender and Recipient
    $mail->setFrom('fortestweb@outlook.com', 'Ms.Banan');
    $mail->addAddress($get_email);

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password Notification';
    $email_template = "
        <h2>Hello $get_name</h2>
        <h3>You are receiving this email because we received a password reset request for your account.</h3>
        <br><br>
        <a href='http://localhost/myproject/settingpassword.php?token=$token&email=$get_email'>Click here to reset your password</a>
    ";
    $mail->Body = $email_template;

    // Send Email
    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error or handle it gracefully
        return false;
    }
}

if (isset($_POST['send_btn'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    $check_email_query = "SELECT email, username FROM user WHERE email=?";
    $stmt = mysqli_prepare($con, $check_email_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $get_email, $get_name);
        mysqli_stmt_fetch($stmt);

        $token = bin2hex(random_bytes(16)); // Generate a secure token

        $update_token_query = "UPDATE user SET token=?, token_creation_time=NOW(), status=1 WHERE email=?";
        $stmt = mysqli_prepare($con, $update_token_query);
        mysqli_stmt_bind_param($stmt, "ss", $token, $get_email);
        $update_token_result = mysqli_stmt_execute($stmt);

        if ($update_token_result && send_password_reset($get_name, $get_email, $token)) {
            $_SESSION['status'] = "An email has been sent to you with instructions to reset your password.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['status'] = "Something went wrong while updating the token or sending the email.";
            header("Location: resetpassword.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "No account found with that email.";
        header("Location: resetpassword.php");
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
</head>
<body>
<div class="container">
    <form action="resetpassword.php" method="POST">
        <div class="title">Reset Your Password</div>
        <div class="user-details">
            <div class="email">
                <label>Email</label>
                <input type="text" placeholder="Enter your email" name="email" required />
            </div>
        </div>
        <div class="button">
            <input type="submit" name="send_btn" value="send" />
        </div>
    </form>
</div>
</body>
</html>
