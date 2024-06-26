<?php
// Include necessary files
require_once 'vendor/autoload.php';

// Start session to retrieve secret key
session_start();

// Check if user is authenticated, otherwise redirect to register page
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['status'] = "Unauthorized access.";
    header('Location: register.php');
    exit();
}

// Include database connection file
include("dbconnection.php");

// Create Google Authenticator instance
$g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
$secretKey = $g->generateSecret(); // Generate secret key for the user

$username = $_SESSION['username']; // Get username from session

// Prepare SQL query to update user's secret key
$query = "UPDATE user SET secret_key = ? WHERE username = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $secretKey, $username); // Bind secret key and username parameters

// Execute the prepared statement to update secret key in the database
if (mysqli_stmt_execute($stmt)) {
    // Generate QR code URL for Google Authenticator
    $qrUrl = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($username, $secretKey, 'websecurity');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Google Authenticator Code</title>
    <link rel="stylesheet" href="css/form.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <form action="scan_qr.php" method="POST">
            <h2>Scan the QR code with Google Authenticator</h2>
            <p>Open the Google Authenticator app and scan the QR code below:</p>
            <!-- Display QR code -->
            <center><img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR Code"></center>
            <p>If you can't scan the QR code, manually enter the following code:</p>
            <!-- Display secret key -->
            <p>Secret Key: <?= htmlspecialchars($secretKey) ?></p><br>
            <!-- Link to login page -->
            <div class="link">Go to login page.<a href="login.php">Login Now</a></div>
        </form>
    </div>
</body>
</html>
<?php
} else {
    // Handle error if failed to update secret key in the database
    echo "Failed to update secret key.";
}
?>
