<?php
session_start();
include("dbconnection.php");
if (!isset($_SESSION['authenticated']) || $_SESSION['role'] != '1') {
    $_SESSION['status'] = "Unauthorized access.";
    header('Location: login.php');
    exit();
}
$username = $_SESSION['username'];
$sql = "SELECT profile_picture_path FROM user WHERE username=?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $profilePicturePath);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
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
            <?php echo $_SESSION['username']; ?><br>
            Number of students you have: 450.<br>
            Your school name: Jordan High School.
        </p>
        <form action="logout.php" method="post">
            <?php if(isset($profilePicturePath)): ?>
                <img src="<?php echo $profilePicturePath; ?>" alt="Profile Picture" style="max-width: 500px; max-height: 500px;">
            <?php else: ?>
                <p>No profile picture found.</p>
            <?php endif; ?>
            <div class="button">
                <input type="submit" name="logout_btn" value="Logout">
            </div>
        </form>
    </div>
</body>

</html>
