<?php
session_start();
if (!isset($_SESSION['auth_user']['authenticated']) || $_SESSION['auth_user']['role'] != '2') {
    $_SESSION['status'] = "Unauthorized access.";
    header('Location: login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/form.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<div class="container">
    <h2 style="text-align:center;">Welcome to Student Dashboard</h2>
    <p style="color: black; font-size: 20px; text-align: center;">Hello student, <?php echo $_SESSION['auth_user']['username']; ?>.</p>
    <p style="color: black; font-size: 16px; text-align: center;">You are currently enrolled in 4 courses.</p>
    <p style="color: black; font-size: 16px; text-align: center;">Your current GPA is 3.7.</p>
    <p style="color: black; font-size: 16px; text-align: center;">Your school name: Jordan High School.</p>
    <form action="logout.php" method="post">
        <div class="button">
            <input type="submit" name="logout_btn" value="Logout" >
        </div>
    </form>
</div>
</body>
</html>

