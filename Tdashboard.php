<?php
session_start();
if (!isset($_SESSION['auth_user']['authenticated']) || $_SESSION['auth_user']['role'] != '1') {
    $_SESSION['status'] = "Unauthorized access.";
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="css/form.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <div class="container">
        <h2 style="text-align:center;">Welcome to Teacher Dashboard</h2>
        <p style="color: black; font-size: 20px; text-align: center;">Hello teacher,
            <?php echo $_SESSION['auth_user']['username']; ?><br>
            Number of students you have: 450.<br>
            Your school name: Jordan High School.
        </p>
        <form action="logout.php" method="post">
        <div class="button">
            <input type="submit" name="logout_btn" value="Logout" >
        </div>
    </form>
    </div>
</body>
</html>