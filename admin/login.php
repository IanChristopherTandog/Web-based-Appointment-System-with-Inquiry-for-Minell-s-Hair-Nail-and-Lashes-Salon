<?php 
session_start();
error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Display errors on the screen

include('partials/dbconnection.php');

// Initialize the $msg variable
$msg = '';

// Fetch the current maintenance mode setting
$query = $con->prepare("SELECT maintenance_mode FROM tblsettings LIMIT 1");
$query->execute();
$query->bind_result($maintenance_mode);
$query->fetch();
$query->close();

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if the email exists in admin table
    $adminQuery = $con->prepare("SELECT ID, Password FROM tbladmin WHERE Email = ?");
    $adminQuery->bind_param("s", $email);
    $adminQuery->execute();
    $adminResult = $adminQuery->get_result();

    if ($adminResult->num_rows > 0) {
        $adminRow = $adminResult->fetch_assoc();

        // Verify the password using password_verify for admin
        if (password_verify($password, $adminRow['Password'])) {
            $_SESSION['salondbaid'] = $adminRow['ID'];
            $_SESSION['email'] = $email; // Store email in session
            header('Location: dashboard.php');
            exit();
        } else {
            $msg = "Invalid email or password.";
        }
    } else {
        // If not admin, check for maintenance mode
        if ($maintenance_mode == 1) {
            $msg = "The salon is currently unavailable due to maintenance. Please try again later.";
        } else {
            // Check if the email and password match stylist credentials
            $stylistQuery = $con->prepare("SELECT * FROM tblstylist WHERE email = ?");
            $stylistQuery->bind_param("s", $email);
            $stylistQuery->execute();
            $stylistResult = $stylistQuery->get_result();

            if ($stylistResult->num_rows > 0) {
                $stylistRow = $stylistResult->fetch_assoc();

                // Verify the password using password_verify for stylists
                if (password_verify($password, $stylistRow['password'])) {
                    $_SESSION['salondbaid'] = $stylistRow['id']; // Use a consistent session variable
                    header('Location: ../stylist/dashboard.php');
                    exit();
                } else {
                    $msg = "Invalid email or password.";
                }
            }

            // If not admin or stylist, check user credentials
            $userQuery = $con->prepare("SELECT * FROM tbluser WHERE email = ?");
            $userQuery->bind_param("s", $email);
            $userQuery->execute();
            $userResult = $userQuery->get_result();

            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();

                // Check if user is verified
                if ($userRow['is_verified'] == 0) {
                    $msg = "Your account is not verified. Please check your email to verify your account.";
                } else {
                    // Verify the password using password_verify for regular users
                    if (password_verify($password, $userRow['password'])) {
                        $_SESSION['userid'] = $userRow['id'];
                        $_SESSION['email'] = $userRow['email'];
                        $_SESSION['name'] = $userRow['name']; // Store name in session
                        header("Location: ../client/homepage.php");
                        exit();
                    } else {
                        $msg = "Invalid email or password.";
                    }
                }
            } else {
                $msg = "Invalid email or password.";
            }

            $stylistQuery->close();
            $userQuery->close();
        }
    }

    $adminQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
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
              <h4>Hello! let's get started</h4>
              <h6 class="font-weight-light">Sign in to continue.</h6>
              <form class="pt-3" method="post" action="">
              <p style="font-size:16px; color:red" align="center"><?php echo $msg; ?></p>
                <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">✉️</span>
                      </div>
                      <input type="email" class="form-control" name="email" placeholder="Email" aria-label="Email" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">🔒</span>
                      </div>
                      <input type="password" class="form-control" name="password" placeholder="Password" aria-label="Passwrod" required>
                    </div>
                  </div>
                <h6 class="font-weight-light"><a href="forgot_password.php" class="text-primary">Forgot your Password</a>?</h6>
                <div class="mt-3">
                  <input type="submit" name="login" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" value="SIGN IN">
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="register.php" class="text-primary">Create</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
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
  <!-- endinject -->
</body>

</html>
