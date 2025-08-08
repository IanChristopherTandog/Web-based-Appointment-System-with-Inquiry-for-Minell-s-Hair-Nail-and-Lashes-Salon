<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:../index.php');
    exit; // Ensure the script stops after redirection
} 

// Fetch the stylist's information using the session ID
$stylistId = $_SESSION['salondbaid'];
$stylistQuery = mysqli_query($con, "SELECT * FROM tblstylist WHERE id = '$stylistId'");
$stylistData = mysqli_fetch_assoc($stylistQuery);

// Get the stylist's name (assuming 'name' is the correct column)
$stylistName = $stylistData['name'];

// Fetch appointments for the stylist
$appointmentsQuery = mysqli_query($con, "SELECT * FROM tblappointment WHERE Stylist = '$stylistName' ORDER BY ApplyDate DESC");
$appointments = mysqli_fetch_all($appointmentsQuery, MYSQLI_ASSOC);

// Fetch status counts (e.g., status of appointments)
$statusCountsQuery = mysqli_query($con, "SELECT Status, COUNT(*) as count FROM tblappointment  WHERE Stylist = '$stylistName' GROUP BY Status");
$statusCounts = [];
while ($row = mysqli_fetch_assoc($statusCountsQuery)) {
    $statusCounts[$row['Status']] = $row['count'];
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
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
</head>
<body>
<div class="container-scroller">
    <?php include_once('partials/_navbar.php'); ?>
    <div class="container-fluid page-body-wrapper">        
        <?php include_once('partials/_sidebar.php'); ?>
        <div class="main-panel">
            <div class="content-wrapper">
                <!-- Appointment Table -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Appointment List</h4>
                    </div>
                    <div class="card-body">
                        <select id="searchDropdown">
                            <option value="">Status Filter</option>
                            <option value="Ongoing">Ongoing (<?php echo isset($statusCounts[1]) ? $statusCounts[1] : 0; ?>)</option>
                            <option value="Cancelled">Cancelled (<?php echo isset($statusCounts[2]) ? $statusCounts[2] : 0; ?>)</option>
                            <option value="Completed">Completed (<?php echo isset($statusCounts[3]) ? $statusCounts[3] : 0; ?>)</option>
                            <option value="Upcoming">Upcoming (<?php echo isset($statusCounts[0]) ? $statusCounts[0] : 0; ?>)</option>
                        </select>  
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Number</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Services</th>
                                        <th>Apply Date</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $row) { ?>
                                        <tr>
                                            <td><?php echo $row['ID']; ?></td>
                                            <td><?php echo $row['AptNumber']; ?></td>
                                            <td><?php echo $row['Name']; ?></td>
                                            <td><?php echo $row['AptDate']; ?></td>
                                            <td><?php echo $row['AptTime']; ?></td>
                                            <td><?php echo $row['Services']; ?></td>
                                            <td><?php echo $row['ApplyDate']; ?></td>
                                            <td><?php echo $row['Remark']; ?></td>
                                            <td class="font-weight-medium">
                                                <?php
                                                if ($row['Status'] == 1) {
                                                    echo '<div class="badge badge-success">Ongoing</div>';
                                                } elseif ($row['Status'] == 2) {
                                                    echo '<div class="badge badge-danger">Cancelled</div>';
                                                } elseif ($row['Status'] == 3) {
                                                    echo '<div class="badge badge-primary">Completed</div>';
                                                } else {
                                                    echo '<div class="badge badge-warning">Upcoming</div>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->
            </div>
        </div>
    </div>
</div>
<?php include_once('partials/logout.php'); ?>

<!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>

    <script>
    $(document).ready(function() {
        var table = new DataTable('#example', {
            order: [[6, 'desc']]
        });

        $('#searchDropdown').on('change', function() {
            var searchTerm = $(this).val();
            table.search(searchTerm).draw();
        });
    });
    </script>
</body>
</html>
