<?php
// Include necessary files
include_once 'vendor/autoload.php'; // Include the autoloader for Google Authenticator

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

// Start session
session_start();

// Initialize variables for button visibility and error message
$showButtons = false;
$errorMessage = '';

// Check if user is authenticated
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['status'] = "Unauthorized access.";
    header('Location: login.php');
    exit();
}

// Check if form is submitted
if (isset($_POST['verify'])) {
    // Retrieve the secret key associated with the user from the session or database
    $secretKey = $_SESSION['secretKey'];

    // Create Google Authenticator instance
    $g = new GoogleAuthenticator();

    // Get the verification code entered by the user
    $code = $_POST['code'];

    if (!empty($code)) {
        $verificationResult = $g->checkCode($secretKey, $code);
        if ($verificationResult) {
            // Code is correct, set variable to show buttons and remove error message
            $showButtons = true;
            $errorMessage = '';
        } else {
            // Incorrect or expired code, set error message
            $errorMessage = 'Incorrect or expired code!';
        }
    } else {
        // Code field is empty, set error message
        $errorMessage = 'Verification code is required!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify Google Authenticator Code</title>
    <link rel="stylesheet" href="css/form.css">
    <style>
        /* CSS for buttons */
        .button {
            text-align: center; /* Center align the buttons */
        }

        .button a.button-link {
            display: inline-block; /* Make the link a block element */
            margin: 10px; /* Add margin around buttons */
        }

        .button a.button-link button {
            height: 35px;
            width: 150px; /* Adjust width as needed */
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 1px;
            cursor: pointer;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            transition: background 0.3s; /* Add transition effect for hover */
        }

        .button a.button-link button:hover {
            background: linear-gradient(-135deg, #71b7e6, #9b59b6);
        }

        /* Additional styles */
        p {
            color: black;
            font-size: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <?php if (!$showButtons): ?>
            <!-- Display verification form if verification is not successful -->
            <form action="verify.php" method="post">
                <div class="user-details">
                    <h1>Verification</h1>
                    <?php
                    // Display error message if set
                    if (!empty($errorMessage)) {
                        echo '<div class="error-message">' . htmlspecialchars($errorMessage) . '</div>';
                    }
                    ?>
                    <div class="input-box">
                        <label>Enter Verification Code:</label>
                        <input type="text" placeholder="Your code" name="code" required />
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="verify" value="Verify" />
                </div>
            </form>
            <?php else: ?>
            <!-- Display greeting message for the authenticated user -->
            <p>Hello, <?php echo $_SESSION['username']; ?></p>
            <!-- Display buttons for dashboard, blog, and password reset if verification is successful -->
            <div class="button">
                <?php if ($_SESSION['role'] == '1'): ?>
                <a href="Tdashboard.php" class="button-link"><button>Dashboard</button></a>
                <a href="blog.php" class="button-link"><button>Go to Blog</button></a>
                <?php else: ?>
                <a href="Sdashboard.php" class="button-link"><button>Go to Dashboard</button></a>
                <a href="blog.php" class="button-link"><button>Go to Blog</button></a>
                <?php endif; ?>
                <a href="changeemail.php" class="button-link"><button>Change Email</button></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
