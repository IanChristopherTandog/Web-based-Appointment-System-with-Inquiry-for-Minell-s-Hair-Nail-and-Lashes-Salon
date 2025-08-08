<?php 
session_start(); // Ensure session is started
include('partials/dbconnection.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Fetch the three newest appointments for the logged-in user
$email = $_SESSION['email']; // Assuming user's email is stored in session
$queryAppointments = mysqli_query($con, "SELECT * FROM tblappointment WHERE Type = 'Online' ORDER BY ApplyDate DESC LIMIT 3");
if (!$queryAppointments) {
    die("Query failed: " . mysqli_error($con));
}
$appointments = mysqli_fetch_all($queryAppointments, MYSQLI_ASSOC);

// Fetch the three newest inquiries for the logged-in user
$queryInquiries = mysqli_query($con, "SELECT * FROM tblinquiry ORDER BY submit_date DESC LIMIT 3");
if (!$queryInquiries) {
    die("Query failed: " . mysqli_error($con));
}
$inquiries = mysqli_fetch_all($queryInquiries, MYSQLI_ASSOC);

// Handle form submission for updating email and password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    // Get the new email and password from the form
    $newEmail = mysqli_real_escape_string($con, $_POST['email']);
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the new password

    // Update the admin settings in the database
    $query = "UPDATE tbladmin SET Email='$newEmail', Password='$newPassword' WHERE Email='{$_SESSION['email']}'";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Update the session email if the email was changed
        $_SESSION['email'] = $newEmail;
        echo "<script>alert('Settings updated successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating settings: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}
?>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="dashboard.php"><img src="images/wogo.png" class="mr-2" alt="Minnel's Salon"/></a>
        <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img src="images/minell-logo-nobg.png" alt="Minnel's Salon"/></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                    <i class="icon-bell mx-0"></i>
                    <span class="count"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                    
                    <!-- Appointment Notifications -->
                    <div class="preview-list">
                      <?php foreach ($appointments as $appointment): ?>
                        <a class="dropdown-item preview-item" href="all-appointments.php">
                          <div class="preview-item-content">
                             <h6 class="preview-subject font-weight-normal"><i class="bi bi-calendar-check"></i><?php echo htmlspecialchars($appointment['Name']); ?> has made an Appointment.</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                              Appointment ID: <?php echo htmlspecialchars($appointment['AptNumber']); ?> | 
                              Appointment Date: <?php echo htmlspecialchars(date('Y-m-d', strtotime($appointment['AptDate']))); ?>
                            </p>
                          </div>
                        </a>
                      <?php endforeach; ?>

                      <!-- Inquiry Notifications -->
                      <?php foreach ($inquiries as $inquiry): ?>
                        <a class="dropdown-item preview-item" href="inquiries.php">
                          <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal"><i class="bi bi-chat-dots"></i>    <?php echo htmlspecialchars($inquiry['name']); ?> has sent an inquiry</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                              <strong>Subject:</strong> <?php echo htmlspecialchars($inquiry['subject']); ?>    |
                              <strong>Date:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($inquiry['submit_date']))); ?>
                            </p>
                          </div>
                        </a>
                      <?php endforeach; ?>
                    </div>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="images/faces/default.jpg" alt="profile"/>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#adminSettingsModal">
                        <i class="ti-settings text-primary"></i>
                        Settings
                    </a>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="ti-power-off text-primary"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>

<!-- Admin Settings Modal -->
<!-- Admin Settings Modal -->
<div class="modal fade" id="adminSettingsModal" tabindex="-1" role="dialog" aria-labelledby="adminSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminSettingsModalLabel">Update Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="settingsForm" method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="update_settings">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


