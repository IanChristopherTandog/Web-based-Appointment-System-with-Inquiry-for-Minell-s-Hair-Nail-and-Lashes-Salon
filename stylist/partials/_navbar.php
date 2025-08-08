<?php
session_start(); // Ensure session is started
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:../index.php');
}

// Fetch the stylist's information using the session ID
$stylistId = $_SESSION['salondbaid'];
$stylistQuery = mysqli_query($con, "SELECT * FROM tblstylist WHERE id = '$stylistId'");
$stylistData = mysqli_fetch_assoc($stylistQuery);

// Get the stylist's name (assuming 'Name' is the correct column)
$stylistName = $stylistData['name'];

$queryAppointments = mysqli_query($con, "SELECT * FROM tblappointment WHERE Stylist = '$stylistName' ORDER BY ApplyDate DESC LIMIT 3 ");
$appointments = mysqli_fetch_all($queryAppointments, MYSQLI_ASSOC);
?>
<link rel="shortcut icon" href="images/favicon.png"/>
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="dashboard.php">
            <img src="images/wogo.png" class="mr-2" alt="Minnel's Salon"/>
        </a>
        <a class="navbar-brand brand-logo-mini" href="dashboard.php">
            <img src="images/minell-logo-nobg.png" alt="Minnel's Salon"/>
        </a>
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
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notification</p>
                    <div class="preview-list" id="notification-list">
                        <?php foreach ($appointments as $appointment): ?>
                            <a class="dropdown-item preview-item" href="appointments.php">
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">
                                        <i class="bi bi-calendar-check"></i>
                                        <?php echo htmlspecialchars($appointment['Name']); ?> has made an Appointment.
                                    </h6>
                                    <p class="font-weight-light small-text mb-0 text-muted">
                                        Service: <?php echo htmlspecialchars($appointment['Services']); ?> |
                                        Date: <?php echo htmlspecialchars(date('Y-m-d', strtotime($appointment['AptDate']))); ?> |
                                        Time: <?php echo htmlspecialchars($appointment['AptTime']); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="images/default.jpg" alt="profile"/>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="account.php"> 
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
