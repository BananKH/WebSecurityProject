<?php 
// Start session to store user data
session_start();

// Include database connection file
include("dbconnection.php");

// Check if the registration form is submitted
if (isset($_POST['register_btn'])){
    // Retrieve user input data
    $username=$_POST['username'];
    $password=$_POST['password'];
    $email=$_POST['email'];
    $confirm_password=$_POST['confirm_password'];
    
    // Check if password and confirm password match
    if ($password != $confirm_password) {
      echo "<script>alert('password mismacth.'); window.location.href='register.php';</script>";
        exit(); // Exit to prevent further execution
    }
    
    // Check if email already exists in the database
    $check_email_query="SELECT email from user WHERE email='$email'";
    $check_email_query_run=mysqli_query($con,$check_email_query);

    if(mysqli_num_rows($check_email_query_run)>0){
      echo "<script>alert('Email already exists.'); window.location.href='register.php';</script>";
        exit(); // Exit to prevent further execution
    } else {
        // Hash the password using the default hashing algorithm
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user with hashed password into the database
        $query="INSERT INTO user (username,password,email,role) VALUES ('$username','$hashed_password','$email','1')";
        $result=mysqli_query($con,$query);

        if($result){
          echo "<script>alert('Registration Successful.'); window.location.href='login.php';</script>";
            exit(); // Exit to prevent further execution
        } else {
           echo "<script>alert('Registration Failed'); window.location.href='register.php';</script>";
            exit(); // Exit to prevent further execution
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
    <div class="title">Signup</div>
    <p>create new account</p>
    <div class="content">
      <form action="register.php" method="POST">
        <div class="user-details">
          <div class="input-box">
            <label>Username</label>
            <input type="text" placeholder="Enter your name" name="username" required/>
          </div>
          <div class="email">
            <label>Email</label>
            <input type="text" placeholder="Enter your email" name="email" required />
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
      <div class="link">Already have an account?<a href="login.php">Login Now</a></div>
    </div>
  </div>
  </script>
</body>
</html>
