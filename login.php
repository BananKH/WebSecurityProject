<?php
// Start session to manage user authentication
session_start();

// Include the database connection file
include("dbconnection.php");

// Check if the login form is submitted
if (isset($_POST['login_now_btn'])) {
  
  // Verify reCAPTCHA
  $response = $_POST['g-recaptcha-response'];
  $secret = "6LftWqgpAAAAAPiTG6F6X1WJIzl8IcvxtdzfGtJy";
  $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response";
  $verification = json_decode(file_get_contents($verifyUrl));

  // Check if reCAPTCHA verification is successful
  if ($verification->success) {
      
      // reCAPTCHA verification successful
      
      // Check if email and password fields are not empty
      if (!empty(trim($_POST['email'])) && !empty(trim($_POST['password']))) {//$_POST from user
          
          // Sanitize and retrieve email and password from the form
          $email = mysqli_real_escape_string($con, $_POST['email']);#remove any special character prevent SQL attack.
          $password = mysqli_real_escape_string($con, $_POST['password']);
          
          // Retrieve the hashed password from the database based on the provided email
          $login_query = "SELECT * FROM user WHERE email='$email'";
          $login_result = mysqli_query($con, $login_query);

          // Check if a user with the provided email exists in the database
          if (mysqli_num_rows($login_result) > 0) {
              $row = mysqli_fetch_assoc($login_result);#to handle each column.
              
              // Extract hashed password from the database
              $hashed_password = $row['password'];
              
              // Verify the entered password with the hashed password
              if (password_verify($password, $hashed_password)) {
                  // Authentication successful, set session variables
                  #verification
                  $_SESSION['auth_user'] = [
                    'authenticated'=>TRUE,
                      'username' => $row['username'],
                      'email' => $row['email'],
                      'role' => $row['role'],
                  ];
                  
                  // Redirect the user based on their role
                  if ($row['role'] == '1') {
                      header("Location: Tdashboard.php");
                      exit();
                  } elseif ($row['role'] == '2') {
                      header("Location: Sdashboard.php");
                      exit();
                  }
              } else {
                  // Invalid email or password, redirect to login page with error message
                  echo "<script>alert('Invalid email or password.'); window.location.href='login.php';</script>";
                  exit();
              }
          } else {
              // User not found in the database, redirect to login page with error message
              echo "<script>alert('Invalid email or password.'); window.location.href='login.php';</script>";
              exit();
          }
      } else {
          // Email or password field is empty, redirect to login page with error message
          echo "<script>alert('All fields are mandatory!'); window.location.href='login.php';</script>";
          exit();
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
  <script src="https://www.google.com/recaptcha/api.js" async defer></script> 
</head>

<body>
  <div class="container"> 
    <div class="title">Log in</div>
     <div class="content">
      <form action="login.php" method="POST">
        <div class="user-details">
          <div class="email">
            <label>Email</label>
            <input type="text" placeholder="Enter your email" name="email" required />
          </div>
          <div class="input-box">
            <label>Password</label>
            <input type="password" placeholder="Enter your password" name="password" required />
          </div>
        </div>
        <div class="g-recaptcha" data-sitekey="6LftWqgpAAAAAHAwh9g8B8OgkUhAyXBkweK9uEgd" ></div>
        <div class="button">
          <input type="submit" name="login_now_btn" value="submit" />
        </div>
        <div class="link" >Not signed up?<a href="register.php">Signup Now</a></div>
      <div class="forgot-password-link" ><a href="resetpassword.php">Forgot Password?</a></div>
      </form>
     
      
    </div> 
  </div>
</body>
</html>
