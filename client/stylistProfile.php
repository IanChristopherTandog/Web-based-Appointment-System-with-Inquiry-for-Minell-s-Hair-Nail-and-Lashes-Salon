<?php 
include('includes/dbconnection.php');
session_start();
error_reporting(0);


// Assuming you store the user's email in the session when they log in
$email = $_SESSION['email'];
$name = $_SESSION['name'];
function checkIfLoggedIn() {
    if (!isset($_SESSION['email'])) {
        // If the user is not logged in, redirect to login.php
        header('Location: ../admin/login.php');
        exit();
    }
  }
  
  checkIfLoggedIn();
  
// Fetch the user's name and email from the database based on their email
$query = mysqli_query($con, "SELECT name, email FROM tblstylist WHERE email='$email'");
$row = mysqli_fetch_array($query);
$name = $row['name'];
$email = $row['name'];  // Optional, as it's already in session

// Handle cancellation request
if (isset($_POST['cancelAppointment'])) {
  $aptNumber = $_POST['aptNumber'];
  $updateStatus = mysqli_query($con, "UPDATE tblappointment SET Status = 2 WHERE AptNumber = '$aptNumber'");
  if ($updateStatus) {
      echo "<script>alert('Appointment has been cancelled.');</script>";
  } else {
      echo "<script>alert('Error cancelling the appointment.');</script>";
  }
}

if (isset($_POST['change_password'])) {
  $currentPassword = $_POST['current_password'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  // Fetch the current password from the database
  $query = mysqli_query($con, "SELECT Password FROM tblstylist WHERE email='$email'");
  $row = mysqli_fetch_array($query);
  $storedPassword = $row['password'];

  // Verify the current password
  if (!password_verify($currentPassword, $storedPassword)) {
      echo "<script>alert('Current password is incorrect.');</script>";
  } else if ($newPassword !== $confirmPassword) {
      echo "<script>alert('New passwords do not match.');</script>";
  } else {
      // Hash the new password
      $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

      // Update the password in the database
      $updatePasswordQuery = mysqli_query($con, "UPDATE tblstylist SET Password='$hashedNewPassword' WHERE email='$email'");
      if ($updatePasswordQuery) {
          echo "<script>alert('Password has been updated successfully.');</script>";
      } else {
          echo "<script>alert('Error updating the password.');</script>";
      }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Minell's Salon</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/minell-logo-nobg.png.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">


</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="homepage.php" class="logo d-flex align-items-center me-auto">
        <!-- <img src="assets/img/minell-logo-nobg.png" alt="Minnel's Salon"> -->
        <h1 class="sitename">Minell's Salon</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="homepage.php" class="active">HOME</a></li>
          <li><a href="appointment.php" class="active">APPOINTMENT</a></li>
          <li class="dropdown"><a href="#"><?php echo htmlspecialchars(strtoupper($name)); ?></span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <!-- <li><a href="view-profile.php">VIEW PROFILE</a></li> -->
              <li><a href="client-inbox.php">MESSAGES</a></li>
              <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title accent-background">
      <div class="container position-relative">
        <h1>VIEW PROFILE</h1>
        <p>VIEW AND EDIT YOUR OWN PROFILE.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="homepage.php">Home</a></li>
            <li class="current">View Profile</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">
    <div class="container light-style flex-grow-1 container-p-y">
    <div class="card overflow-hidden">
        <div class="row g-0">
            <div class="col-md-3 pt-0">
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list"
                        href="#account-info">Schedule/Reservation</a>
                    <a class="list-group-item list-group-item-action active" data-bs-toggle="list"
                        href="#account-general">General</a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list"
                        href="#account-change-password">Change password</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="account-general">
                        <div class="card-body d-flex align-items-center">
                            <img src="assets/img/default.png" alt class="d-block ui-w-80">
                            <div class="ms-4">
                                <label class="btn btn-outline-primary">
                                    Upload new photo
                                    <input type="file" class="account-settings-fileinput d-none">
                                </label>
                                <button type="button" class="btn btn-default">Reset</button>
                                <div class="text-muted small mt-1">Allowed JPG, GIF or PNG.</div>
                            </div>
                        </div>
                        <hr class="border-light m-0">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($name); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="appointment_email" name="email"
                                    value="<?php echo $email; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="account-change-password">
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Current password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Repeat new password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="account-info">
                        <div class="card-body">
                            <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Appointment Number</th>
                                            <th>Appointment Date</th>
                                            <th>Appointment Time</th>
                                            <th>Services</th>
                                            <th>Apply Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = mysqli_query($con, "SELECT * FROM tblappointment WHERE email='$email'");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $row['AptNumber']; ?></td>
                                                <td><?php echo $row['AptDate']; ?></td>
                                                <td><?php echo $row['AptTime']; ?></td>
                                                <td><?php echo $row['Services']; ?></td>
                                                <td><?php echo $row['ApplyDate']; ?></td>
                                                <td class="fw-medium">
                                                    <?php
                                                    if ($row['Status'] == 0) {
                                                        echo '<div class="badge bg-warning">Upcoming</div>'; // Yellow
                                                    } elseif ($row['Status'] == 1) {
                                                        echo '<div class="badge bg-success">On going</div>'; // Green
                                                    } elseif ($row['Status'] == 2) {
                                                        echo '<div class="badge bg-danger">Cancelled</div>'; // Red
                                                    } elseif ($row['Status'] == 3) {
                                                        echo '<div class="badge bg-primary">Completed</div>'; // Blue
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['Status'] != 2) { ?>
                                                        <button class="badge bg-danger" onclick="confirmCancel('<?php echo $row['AptNumber']; ?>')">Cancel</button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="border-light m-0">
                    </div>

                    <div class="tab-pane fade" id="account-social-links">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="text" class="form-control" value="https://twitter.com/user">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="text" class="form-control" value="https://www.facebook.com/user">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="text" class="form-control" value="https://www.instagram.com/user">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="account-notifications">
                        <div class="card-body">
                            <h6 class="mb-4">Activity</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch1" checked>
                                <label class="form-check-label" for="switch1">Email me when someone comments on my article</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch2" checked>
                                <label class="form-check-label" for="switch2">Email me when someone answers on my forum thread</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch3">
                                <label class="form-check-label" for="switch3">Email me when someone follows me</label>
                            </div>
                        </div>
                        <hr class="border-light m-0">
                        <div class="card-body">
                            <h6 class="mb-4">Application</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch4" checked>
                                <label class="form-check-label" for="switch4">News and announcements</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch5">
                                <label class="form-check-label" for="switch5">Weekly product updates</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch6" checked>
                                <label class="form-check-label" for="switch6">Weekly blog digest</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="text-end mt-3">
            <button type="button" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn btn-default">Cancel</button>
        </div> -->
    </div>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel your appointment?
                    <a href="cancellation-terms.php">Terms and Condition</a>
                </div>
                <div class="modal-footer">
                    <form method="post" action="">
                        <input type="hidden" id="aptNumber" name="aptNumber">
                        <button type="submit" class="btn btn-danger" name="cancel_appointment">Yes, Cancel</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmCancel(aptNumber) {
            document.getElementById('aptNumber').value = aptNumber;
            var cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'), {
                keyboard: false
            });
            cancelModal.show();
        }
    </script>



</section>


  </main>

  <footer id="footer" class="footer accent-background">

  <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">Minell's Salon</span>
          </a>
          <p>We pride ourselves on our high quality work and attention to detail. The products we use are of top quality branded products.</p>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <!-- <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li> -->
          </ul>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Our Services</h4>
          <!-- <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul> -->
        </div>

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4>Contact Us</h4>
          <p>Langkaan,</p>
          <p>Dasmarinas City, Cavite</p>
          <p class="mt-4"><strong>Phone:</strong> <span>+639683622371</span></p>
          <p><strong>Email:</strong> <span>minells.salon@gmail.com</span></p>
        </div>

      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- jQuery script for modal functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
  new DataTable('#example', {
            order: [[1, 'desc']]
        });
</script>

<script>
      window.embeddedChatbotConfig = {
      chatbotId: "sd6FoUlgpRjbJQJDuNyK0",
      domain: "www.chatbase.co"
      }
    </script>

    <script
      src="https://www.chatbase.co/embed.min.js"
      chatbotId="sd6FoUlgpRjbJQJDuNyK0"
      domain="www.chatbase.co"
      defer>
    </script>
</body>

</html>