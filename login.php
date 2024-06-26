<?php
// Start session to manage user authentication
session_start();

// Include the database connection file
include("dbconnection.php");

// Check if the login form is submitted
if (isset($_POST['login_now_btn']) && isset($_FILES['file'])) {
    // Verify reCAPTCHA
    $response = $_POST['g-recaptcha-response'];
    $secret = "6LcyqsspAAAAACZ-xOfUCnUh_r7xST0bySKzC9nz"; // Replace with your reCAPTCHA secret key
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response";
    $verification = json_decode(file_get_contents($verifyUrl));

    // Check if reCAPTCHA verification is successful
    if ($verification->success) {
        // Check if email, password, and file fields are not empty
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            // Retrieve email and password from the form
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Function to check if the uploaded file is an image
            function isImage($file) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Allowed image types
                return in_array($file['type'], $allowedTypes);
            }
          
            // Function to check if the uploaded file content is a valid image
            function isValidImage($file) {
                $image = getimagesize($file['tmp_name']);
                return $image !== false;
            }

            // Check for upload errors
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                // Check if the uploaded file is an image
                if (!isImage($_FILES['file'])) {
                    echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');window.location.href='login.php'</script>";
                    exit();
                }

                // Check if the uploaded file content is a valid image
                if (!isValidImage($_FILES['file'])) {
                    echo "<script>alert('Invalid image file.'); window.location.href='login.php';</script>";
                    exit();
                }

                // Retrieve file details
                $uploadDirectory = 'uploads/';
                $filename = uniqid() . '_' . basename($_FILES['file']['name']); // Use basename to prevent directory traversal
                $destination = $uploadDirectory . $filename;
                $filetype = $_FILES['file']['type'];
                $filesize = $_FILES['file']['size'];
                $filedata = file_get_contents($_FILES['file']['tmp_name']);

                // Prepare SQL statement to insert file data into database
                $insert_query = "INSERT INTO files (filename, filetype, filesize, filedata) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $insert_query);
                mysqli_stmt_bind_param($stmt, "ssis", $filename, $filetype, $filesize, $filedata);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    // Move uploaded file to destination
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
                        // Update the user's profile picture path in the database
                        $profilePicturePath = $destination;

                        // Prepare SQL statement to update profile picture path
                        $sql = "UPDATE user SET profile_picture_path = ? WHERE email = ?";
                        $stmt = mysqli_prepare($con, $sql);
                        mysqli_stmt_bind_param($stmt, "ss", $profilePicturePath, $email);
                        mysqli_stmt_execute($stmt);

                        // Execute the statement
                        $login_query = "SELECT * FROM user WHERE email=?";

                        // Prepare the statement
                        $stmt = mysqli_prepare($con, $login_query);

                        // Bind parameters
                        mysqli_stmt_bind_param($stmt, "s", $email);

                        // Execute the statement
                        mysqli_stmt_execute($stmt);

                        // Get the result
                        $login_result = mysqli_stmt_get_result($stmt);

                        // Check if a user with the provided email exists in the database
                        if (mysqli_num_rows($login_result) > 0) {
                            $row = mysqli_fetch_assoc($login_result);
                            
                            // Verify the entered password with the hashed password
                            if (password_verify($password, $row['password'])) {
                                $secretKey = $row['secret_key'];
                                if ($secretKey) {
                                    // Authentication successful, set session variables
                                    $_SESSION['authenticated'] = TRUE;
                                    $_SESSION['username'] = $row['username'];
                                    $_SESSION['secretKey'] = $secretKey;
                                    $_SESSION['role'] = $row['role'];

                                    // Redirect the user to verify.php
                                    header("Location: verify.php");
                                    exit();
                                } else {
                                    echo "<script>alert('Invalid OTP.'); window.location.href='login.php';</script>";
                                    exit();
                                }
                            }
                        }
                    } else {
                        echo "<script>alert('Failed to move uploaded file.'); window.location.href='login.php';</script>";
                        exit();
                    }
                } else {
                    echo "<script>alert('Failed to insert file data into database.'); window.location.href='login.php';</script>";
                    exit();
                }
            } else {
                // Email, password, or file field is empty, redirect to login page with error message
                echo "<script>alert('All fields are mandatory!'); window.location.href='login.php';</script>";
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
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
            <form action="login.php" method="POST" onsubmit="return validateForm()" enctype="multipart/form-data">
                <div class="user-details">
                    <div class="email">
                        <label>Email</label>
                        <input type="text" placeholder="Enter your email" name="email" required />
                    </div>
                    <div class="input-box">
                        <label>Password</label>
                        <input type="password" placeholder="Enter your password" name="password" required />
                    </div>
                    <div>
                        <p>Upload a File</p>
                        <label for="file">Select file</label>
                        <input type="file" name="file" id="file">
                    </div>
                </div>
                <div class="g-recaptcha" data-sitekey="6LcyqsspAAAAAO9CxaeUiBkKrq0ccnahKc0S274o"></div>
                <div class="button">
                    <input type="submit" name="login_now_btn" value="submit" />
                </div>
                <div class="link">Not signed up?<a href="register.php">Signup Now</a></div>
                <div class="forgot-password-link"><a href="resetpassword.php">Forgot Password?</a></div>
            </form>
        </div>
    </div>
</body>

</html>
