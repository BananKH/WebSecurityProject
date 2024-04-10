<?php
// Start session to manage user authentication
session_start();
include("dbconnection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function send_password_reset($get_name, $get_email, $token) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'fortestweb@outlook.com';
    $mail->Password = 'websecurity2024';
    $mail->SMTPSecure = 'tls';

    $mail->setFrom('fortestweb@outlook.com', 'Ms.Banan');
    $mail->addAddress($get_email); // Change recipient email here

    $mail->isHTML(true);
    $mail->Subject = 'Reset Password Notification';

    $email_template = "
        <h2>Hello</h2>
        <h3>You are receiving this email because we received a password reset request for your account.</h3>
        <br><br>
        <a href='http://localhost/myproject/settingpassword.php?token=$token&email=$get_email'>Click Me</a>
    ";

    $mail->Body = $email_template;

    // Send the email
    $mail->send();
}
if (isset($_POST['send_btn'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);#email from user
    $token = md5(rand());


    #check if the email in database.
    $check_email_query = "SELECT email FROM user WHERE email='$email'";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($check_email_query_run) > 0) {
        $row = mysqli_fetch_array($check_email_query_run);
        $get_name = $row['username'];
        $get_email = $row['email'];

        $update_token = "UPDATE user SET token='$token' ,token_creation_time=NOW(), status=1  WHERE email='$get_email'";
        $update_token_run = mysqli_query($con, $update_token);
        if ($update_token_run) {
            send_password_reset($get_name, $get_email, $token);
            $_SESSION['status'] = "An email has been sent to you with instructions to reset your password.";
            // Redirect the user to the appropriate page
            header("Location: login.php");
            exit(0);
        } else {
           echo $_SESSION['status'] = "Something went wrong while updating the token.";
            header("Location: resetpassword.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "No account found with that email.";
        header("Location: resetpassword.php");
        exit(0);
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