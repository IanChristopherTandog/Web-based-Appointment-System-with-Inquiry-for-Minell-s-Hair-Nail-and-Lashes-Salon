<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('partials/dbconnection.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure Composer's autoload is included

// Initialize the $msg variable
$msg = '';
$showLoginButton = false;

function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kawamatsumachi@gmail.com'; // Your SMTP email
        $mail->Password   = 'hnlnepjapbvbsadw';    // Your SMTP password
        $mail->SMTPSecure = 'tls';                    // Enable TLS encryption
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('kawamatsumachi@gmail.com', 'Minnel\'s Salon Support'); // Sender's email
        $mail->addAddress($email); // Recipient's email

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; color: #333;'>
            <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h2 style='color: #FFDF00; margin-bottom: 0;'>Minnel's Salon</h2>
                    <p style='color: #666; font-size: 14px;'>Beauty & Style at Your Fingertips</p>
                </div>
                <div style='padding: 30px; text-align: center;'>
                    <div style='font-size: 16px; color: #333; margin-bottom: 20px;'>
                        We received a request to reset your password. Use the code below to reset your password:
                    </div>
                    <div style='font-size: 24px; font-weight: bold; color: #000000; margin-bottom: 20px;'>
                        $otp
                    </div>
                    <div style='font-size: 14px; color: #666;'>
                        Please enter this code on the website to reset your password. If you didn’t request a password reset, you can safely ignore this email.
                    </div>
                </div>
                <div style='padding: 20px; background-color: #f0f0f0; text-align: center; font-size: 12px; color: #999;'>
                    This email was sent by <b>Minell's Salon</b>. If you did not make this request, please contact support.
                </div>
            </div>
        </div>
    ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['send_code'])) {
    $email = trim($_POST['email']);

    // Check if the email exists in the user table
    $userQuery = $con->prepare("SELECT * FROM tbluser WHERE email = ?");
    $userQuery->bind_param("s", $email);
    $userQuery->execute();
    $userResult = $userQuery->get_result();

    if ($userResult->num_rows > 0) {
        // Generate a 6-digit OTP code
        $otp = rand(100000, 999999);

        // Store the OTP in session and send it via email
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = $otp;

        // Send the OTP using PHPMailer
        if (sendOtpEmail($email, $otp)) {
            $msg = "A 6-digit code has been sent to your email. Please check your inbox.";
        } else {
            $msg = "Failed to send email. Please try again.";
        }
    } else {
        $msg = "Email does not exist.";
    }

    $userQuery->close();
}

if (isset($_POST['verify_code'])) {
    // Combine the six input boxes into a single string
    $entered_otp = $_POST['otp_1'] . $_POST['otp_2'] . $_POST['otp_3'] . $_POST['otp_4'] . $_POST['otp_5'] . $_POST['otp_6'];
    $new_password = trim($_POST['new_password']);

    // Verify if the entered OTP matches the one stored in session
    if ($entered_otp == $_SESSION['otp']) {
        $email = $_SESSION['reset_email'];

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateQuery = $con->prepare("UPDATE tbluser SET password = ? WHERE email = ?");
        $updateQuery->bind_param("ss", $hashed_password, $email);
        if ($updateQuery->execute()) {
            $msg = "Password successfully updated. You can now log in with your new password.";
            $showLoginButton = true;
        } else {
            $msg = "Failed to update password. Please try again.";
        }

        // Clear the OTP and email session variables
        unset($_SESSION['otp']);
        unset($_SESSION['reset_email']);

        $updateQuery->close();
    } else {
        $msg = "Invalid OTP code. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Minell's Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />

  <style>
  .otp-input {
    width: 40px;
    height: 40px;
    font-size: 24px;
    text-align: center;
    margin: 0 10px; /* Adding horizontal margin between inputs */
    padding: 5px; /* Optional padding */
    border: 1px solid #ccc;
    border-radius: 5px;
  }
</style>
</head>

<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="images/minnel-logo.svg" alt="logo">
              </div>
              <h4>Forgot Password</h4>
              <p style="font-size:16px; color:red" align="center"><?php echo $msg; ?></p>

              <?php if ($showLoginButton): ?>
                <!-- Show the login button if the password is updated -->
                <div class="mt-3">
                  <a href="login.php" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Login</a>
                </div>
              <?php elseif (!isset($_SESSION['otp'])): ?>
              <!-- Step 1: Enter email to receive OTP -->
              <form class="pt-3" method="post" action="">
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mt-3">
                  <input type="submit" name="send_code" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" value="Send OTP">
                </div>
                <div class="text-end mt-4 font-weight-light">
                  <a href="login.php" class="text-primary">Login</a> |
                  <a href="register.php" class="text-primary">Register</a>
                </div>

              </form>
              <?php else: ?>
              <!-- Step 2: Enter OTP and new password -->
              <form class="pt-3" method="post" action="">
                <div class="form-group d-flex justify-content-center">
                  <input type="text" class="otp-input form-control" name="otp_1" maxlength="1" required oninput="moveToNext(this, 'otp_2')">
                  <input type="text" class="otp-input form-control" name="otp_2" maxlength="1" required oninput="moveToNext(this, 'otp_3')">
                  <input type="text" class="otp-input form-control" name="otp_3" maxlength="1" required oninput="moveToNext(this, 'otp_4')">
                  <input type="text" class="otp-input form-control" name="otp_4" maxlength="1" required oninput="moveToNext(this, 'otp_5')">
                  <input type="text" class="otp-input form-control" name="otp_5" maxlength="1" required oninput="moveToNext(this, 'otp_6')">
                  <input type="text" class="otp-input form-control" name="otp_6" maxlength="1" required oninput="moveToNext(this, '')">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="mt-3">
                  <input type="submit" name="verify_code" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" value="Reset Password">
                </div>
              </form>
              <?php endif; ?>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>

  <script>
    // JavaScript function to auto-focus the next input field
    function moveToNext(current, nextFieldID) {
      if (current.value.length === 1 && nextFieldID !== '') {
        document.getElementsByName(nextFieldID)[0].focus();
      }
    }
  </script>
</body>

</html>
