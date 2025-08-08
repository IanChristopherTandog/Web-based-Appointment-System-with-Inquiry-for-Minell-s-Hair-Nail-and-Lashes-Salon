<?php 
include('includes/dbconnection.php');
session_start();
error_reporting(0);

function checkIfLoggedIn() {
    if (!isset($_SESSION['email'])) {
        // If the user is not logged in, redirect to login.php
        header('Location: ../admin/login.php');
        exit();
    }
  }
  
  checkIfLoggedIn();
  
// Assuming you store the user's email in the session when they log in
$email = $_SESSION['email'];
$name = $_SESSION['Name'];


// Fetch the user's name and email from the database based on their email
$query = mysqli_query($con, "SELECT Name, Email FROM tbluser WHERE Email='$email'");
$row = mysqli_fetch_array($query);
$name = $row['Name'];
$email = $row['Email'];  // Optional, as it's already in session

// Count the number of appointments for the logged-in user
$appointmentCountQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM tblappointment WHERE Email='$email'");
$appointmentCountRow = mysqli_fetch_array($appointmentCountQuery);
$appointmentCount = $appointmentCountRow['total'];

// Count the number of inquiries for the logged-in user based on their email
$inquiryCountQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM tblinquiry WHERE Email='$email'");
$inquiryCountRow = mysqli_fetch_array($inquiryCountQuery);

// Check if the result is set and assign 0 if no inquiries are found
$inquiryCount = isset($inquiryCountRow['total']) ? $inquiryCountRow['total'] : 0;

// Now you can use $inquiryCount to display the number of inquiries

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
  $query = mysqli_query($con, "SELECT Password FROM tbluser WHERE Email='$email'");
  $row = mysqli_fetch_array($query);
  $storedPassword = $row['Password'];

  // Verify the current password
  if (!password_verify($currentPassword, $storedPassword)) {
      echo "<script>alert('Current password is incorrect.');</script>";
  } else if ($newPassword !== $confirmPassword) {
      echo "<script>alert('New passwords do not match.');</script>";
  } else {
      // Hash the new password
      $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

      // Update the password in the database
      $updatePasswordQuery = mysqli_query($con, "UPDATE tbluser SET Password='$hashedNewPassword' WHERE Email='$email'");
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
    <link href="assets/img/minell-logo-nobg.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- Font Awesome -->
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    <!-- CSS -->
    <link href="assets/css/view-profile.css" rel="stylesheet">
      <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

      <!-- DataTable CSS  -->

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
  </head>
  <body>

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="homepage.php" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/minell-logo-nobg.png" alt="Minnel's Salon">
        <h1 class="sitename">Minell's Salon</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="homepage.php" >HOME</a></li>
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

    <div class="header__wrapper">
      <header></header>
      <div class="cols__container">
        <div class="left__col">
          <div class="img__container">
            <img src="assets/img/default-profile.jpg" alt="user-profile"/>
            <span></span>
          </div>
          <h2><?php echo htmlspecialchars($name); ?></h2>
          <p>Beloved Client</p>
          <p><?php echo $email; ?></p>

          <ul class="about">
            <li><span><?php echo $appointmentCount; ?></span> Appointments</li>
            <li><span><?php echo $inquiryCount;?></span>Inquiries</li>
            <li><span>200,543</span>Attraction</li>
          </ul>

          <div class="content">
            <p>
              Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam
              erat volutpat. Morbi imperdiet, mauris ac auctor dictum, nisl
              ligula egestas nulla.
            </p>

            <!-- <ul>
              <li><i class="fab fa-twitter"></i></li>
              <i class="fab fa-pinterest"></i>
              <i class="fab fa-facebook"></i>
              <i class="fab fa-dribbble"></i>
            </ul> -->
          </div>
        </div>
        <div class="right__col">
          <nav>
            <ul>
              <li><a href="#" data-tab="Appointments" class="tab-link active-link">Appointments</a></li>
              <li><a href="#" data-tab="history" class="tab-link">History</a></li>
              <li><a href="#" data-tab="settings" class="tab-link">Settings</a></li>
            </ul>
            <!-- <button>Follow</button> -->
          </nav>
          <div class="tab-content" id="Appointments">
              <p>This is the list of your appointments</p>
              <div class="container p-3 my-5">
                <table id="example" class="table table-striped" style="width:100%">
                          <thead>
                              <tr>
                                  <th>Appointment Number</th>
                                  <th>Appointment Date</th>
                                  <th>Appointment Time</th>
                                  <th>Services</th>
                                  <th>Stylist</th>
                                  <th>Apply Date</th>
                                  <th>Type</th>
                                  <th>Status</th>
                                  <th>Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              $currentDateTime = new DateTime(); // Get current date and time
                              $ret = mysqli_query($con, "SELECT * FROM tblappointment WHERE Email='$email' AND Status IN (0, 1)");
                              $cnt = 1;

                              while ($row = mysqli_fetch_array($ret)) {
                                  // Combine appointment date and time into a DateTime object
                                  $appointmentDateTime = new DateTime($row['AptDate'] . ' ' . $row['AptTime']);

                                  // Check if appointment time has passed and status is Upcoming
                                  if ($currentDateTime >= $appointmentDateTime && $row['Status'] == 0) {
                                      $row['Status'] = 1; // Set status to "Ongoing"
                                      // Optionally, update the status in the database if needed
                                      // mysqli_query($con, "UPDATE tblappointment SET Status = 1 WHERE AptNumber = '{$row['AptNumber']}'");
                                  }

                                  // Check if one day has passed since the appointment date and time
                                  $interval = $currentDateTime->diff($appointmentDateTime);
                                  if ($interval->days >= 1 && $row['Status'] == 1) {
                                      $row['Status'] = 3; // Set status to "Completed"
                                      // Optionally, update the status in the database if needed
                                      // mysqli_query($con, "UPDATE tblappointment SET Status = 3 WHERE AptNumber = '{$row['AptNumber']}'");
                                  }
                              ?>
                              <tr>
                                  <td><?php echo $row['AptNumber']; ?></td>
                                  <td><?php echo $row['AptDate']; ?></td>
                                  <td><?php echo $row['AptTime']; ?></td>
                                  <td><?php echo $row['Services']; ?></td>
                                  <td><?php echo $row['Stylist']; ?></td>
                                  <td><?php echo $row['ApplyDate']; ?></td>
                                  <td><?php echo $row['Type']; ?></td>
                                  <td class="fw-medium">
                                      <?php
                                      if ($row['Status'] == 0) {
                                          echo '<div class="badge bg-warning">Upcoming</div>'; // Yellow
                                      } elseif ($row['Status'] == 1) {
                                          echo '<div class="badge bg-success">Ongoing</div>'; // Green
                                      } elseif ($row['Status'] == 2) {
                                          echo '<div class="badge bg-danger">Cancelled</div>'; // Red
                                      } elseif ($row['Status'] == 3) {
                                          echo '<div class="badge bg-primary">Completed</div>'; // Blue
                                      } elseif ($row['Status'] == 4) {
                                          echo '<div class="badge bg-secondary">No-Show</div>'; // Gray
                                      }
                                      ?>
                                  </td>
                                  <td>
                                      <?php if ($row['Status'] != 2 && $row['Status'] != 3) { ?>
                                          <button class="badge bg-danger" onclick="confirmCancel('<?php echo $row['AptNumber']; ?>')">Cancel</button>
                                      <?php } ?>
                                  </td>
                              </tr>
                              <?php } ?>
                          </tbody>
                      </table>
                  </div>
          </div>
          <div class="tab-content" id="history">
            <p>This is the list of your past appointments</p>
                  <div class="container p-3 my-5">
                      <table id="past" class="table table-striped" style="width:100%">
                          <thead>
                              <tr>
                                  <th>Appointment Number</th>
                                  <th>Appointment Date</th>
                                  <th>Appointment Time</th>
                                  <th>Services</th>
                                  <th>Stylist</th>
                                  <th>Apply Date</th>
                                  <th>Status</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              $currentDateTime = new DateTime(); // Get current date and time
                              $ret = mysqli_query($con, "SELECT * FROM tblappointment WHERE Email='$email' AND Status IN (2, 3, 4)");
                              $cnt = 1;

                              while ($row = mysqli_fetch_array($ret)) {
                                  // Combine appointment date and time into a DateTime object
                                  $appointmentDateTime = new DateTime($row['AptDate'] . ' ' . $row['AptTime']);

                                  // Check if appointment time has passed and status is Upcoming
                                  if ($currentDateTime >= $appointmentDateTime && $row['Status'] == 0) {
                                      $row['Status'] = 1; // Set status to "Ongoing"
                                      // Optionally, update the status in the database if needed
                                      // mysqli_query($con, "UPDATE tblappointment SET Status = 1 WHERE AptNumber = '{$row['AptNumber']}'");
                                  }

                                  // Check if one day has passed since the appointment date and time
                                  $interval = $currentDateTime->diff($appointmentDateTime);
                                  if ($interval->days >= 1 && $row['Status'] == 1) {
                                      $row['Status'] = 3; // Set status to "Completed"
                                      // Optionally, update the status in the database if needed
                                      // mysqli_query($con, "UPDATE tblappointment SET Status = 3 WHERE AptNumber = '{$row['AptNumber']}'");
                                  }
                              ?>
                              <tr>
                                  <td><?php echo $row['AptNumber']; ?></td>
                                  <td><?php echo $row['AptDate']; ?></td>
                                  <td><?php echo $row['AptTime']; ?></td>
                                  <td><?php echo $row['Services']; ?></td>
                                  <td><?php echo $row['Stylist']; ?></td>
                                  <td><?php echo $row['ApplyDate']; ?></td>
                                  <td class="fw-medium">
                                      <?php
                                      if ($row['Status'] == 0) {
                                          echo '<div class="badge bg-warning">Upcoming</div>'; // Yellow
                                      } elseif ($row['Status'] == 1) {
                                          echo '<div class="badge bg-success">Ongoing</div>'; // Green
                                      } elseif ($row['Status'] == 2) {
                                          echo '<div class="badge bg-danger">Cancelled</div>'; // Red
                                      } elseif ($row['Status'] == 3) {
                                          echo '<div class="badge bg-primary">Completed</div>'; // Blue
                                      } elseif ($row['Status'] == 4) {
                                          echo '<div class="badge bg-secondary">No-Show</div>'; // Gray
                                      }
                                      ?>
                                  </td>
                              </tr>
                              <?php } ?>
                          </tbody>
                      </table>
                </div>
          </div>

          <div class="tab-content" id="settings">
            <p>Would like to update you password?</p>
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
      </div>
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
                        <button type="submit" class="btn btn-danger" name="cancelAppointment">Yes, Cancel</button>
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
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable for #example table
        $('#example').DataTable({
            responsive: true,  // Make it responsive
            order: [[1, 'desc']]  // Apply descending order on the second column
        });

        // Initialize DataTable for #past table
        $('#past').DataTable({
            order: [[1, 'desc']]  // Apply descending order on the second column
        });
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

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');

        // Function to clear active states
        function clearActiveStates() {
          tabLinks.forEach(link => link.classList.remove('active-link'));
          tabContents.forEach(content => content.classList.remove('active'));
        }

        // Add click event listener to each tab link
        tabLinks.forEach(link => {
          link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default anchor behavior

            // Clear previous active states
            clearActiveStates();

            // Set the clicked link as active
            this.classList.add('active-link');

            // Get the tab to show
            const tabId = this.getAttribute('data-tab');
            const activeTab = document.getElementById(tabId);

            // Display the active tab content
            activeTab.classList.add('active');
          });
        });

        // Initialize by displaying the first tab
        clearActiveStates();
        tabLinks[0].click(); // Simulate click on the first tab to display it
      });
    </script>
  </body>
</html>
