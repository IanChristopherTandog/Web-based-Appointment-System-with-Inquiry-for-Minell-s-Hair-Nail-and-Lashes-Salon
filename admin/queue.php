<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Update appointment
    if (isset($_POST['updateAppointment'])) {
        $aptId = $_POST['aptId'];
        $aptNumber = $_POST['aptNumber'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $aptDate = $_POST['aptDate'];
        $services = $_POST['services'];
        $remark = $_POST['remark'];
        $status = $_POST['status'];

        // Update the appointment data
        $query = mysqli_query($con, "UPDATE tblappointment SET AptNumber='$aptNumber', Name='$name', Email='$email', PhoneNumber='$phoneNumber', AptDate='$aptDate', Services='$services', Remark='$remark', Status='$status' WHERE ID='$aptId'");

        // Check if the update was successful
        if ($query) {
            echo "<script>alert('Queue has been updated.');</script>";
            echo "<script>window.location.href = 'queue.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    // Fetch appointments for the table
     $appointmentsQuery = mysqli_query($con, "SELECT * FROM tblappointment WHERE AptDate = CURDATE() AND Status = 0 ORDER BY ApplyDate DESC");
    // $appointmentsQuery = mysqli_query($con, "SELECT * FROM tblappointment ORDER BY ApplyDate DESC");
    // Fetch appointments for the table
    $ServingQuery = mysqli_query($con, "SELECT * FROM tblappointment WHERE AptDate = CURDATE() AND Status = 1 ORDER BY ApplyDate DESC");
    // $appointmentsQuery = mysqli_query($con, "SELECT * FROM tblappointment ORDER BY ApplyDate DESC");

    // Fetch status counts
    $statusCountsQuery = mysqli_query($con, "SELECT Status, COUNT(*) as count FROM tblappointment WHERE AptDate = CURDATE() GROUP BY Status");
    $statusCounts = [];
    while ($row = mysqli_fetch_assoc($statusCountsQuery)) {
        $statusCounts[$row['Status']] = $row['count'];
    }
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
                <!-- Modal for Editing Appointments -->
                <div class="modal fade" id="editAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editAppointmentModalLabel">Edit Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="queue.php">
                                    <input type="hidden" id="editAppointmentId" name="aptId">
                                    <div class="row">
                                        <div class="col-md-6"> <!-- Left Column -->
                                            <div class="form-group">
                                                <label for="editAptNumber">Appointment Number:</label>
                                                <input type="text" class="form-control" id="editAptNumber" name="aptNumber" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="editName">Name:</label>
                                                <input type="text" class="form-control" id="editName" name="name" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="editEmail">Email:</label>
                                                <input type="email" class="form-control" id="editEmail" name="email" required readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6"> <!-- Right Column -->
                                            <div class="form-group">
                                                <label for="editAptDate">Appointment Date:</label>
                                                <input type="text" class="form-control" id="editAptDate" name="aptDate" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="editServices">Services:</label>
                                                <input type="text" class="form-control" id="editServices" name="services" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="editStatus">Status:</label>
                                                <select id="editStatus" name="status" class="form-control" required>
                                                    <option value="0" <?php if ($row['Status'] == "0") echo "selected disabled"; ?>>Upcoming</option>
                                                    <option value="1" <?php if ($row['Status'] == "1") echo "selected"; ?>>Ongoing</option>
                                                    <option value="2" <?php if ($row['Status'] == "2") echo "selected"; ?>>Cancelled</option>
                                                    <option value="3" <?php if ($row['Status'] == "3") echo "selected"; ?>>Completed</option>
                                                    <option value="4" <?php if ($row['Status'] == "4") echo "selected"; ?>>No-Show</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="updateAppointment" class="btn btn-primary mr-2">Update</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Now Serving Queue Table -->
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                        <h4 class="card-title al">Now Serving</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Stylist</th>
                                        <th>QN</th>
                                        <th>Name</th>
                                        <th>Service(s)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($ServingQuery)) { ?>
                                        <tr>
                                            <td><?php echo $row['Stylist']; ?></td>
                                            <td><?php echo $row['QueueNumber']; ?></td>
                                            <td><?php echo $row['Name']; ?></td>
                                            <td><?php echo $row['Services']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAppointmentModal"
                                                    onclick="editAppointment('<?php echo $row['ID']; ?>', '<?php echo $row['AptNumber']; ?>', '<?php echo $row['Name']; ?>', 
                                                    '<?php echo $row['Email']; ?>', '<?php echo $row['PhoneNumber']; ?>', '<?php echo $row['AptDate']; ?>', '<?php echo $row['Services']; ?>', '<?php echo $row['Remark']; ?>', 
                                                    '<?php echo $row['Status']; ?>')">View</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
                
                <!-- Queue Table -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Queue List</h4>
                    </div>
                    <div class="card-body">  
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>QN</th>
                                        <th>Stylist</th>
                                        <th>Name</th>
                                        <th>Service(s)</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($appointmentsQuery)) { ?>
                                        <tr>
                                            <td><?php echo $row['QueueNumber']; ?></td>
                                            <td><?php echo $row['Stylist']; ?></td>
                                            <td><?php echo $row['Name']; ?></td>
                                            <td><?php echo $row['Services']; ?></td>
                                            <td class="font-weight-medium">
                                                <?php
                                                if ($row['Status'] == 0) {
                                                    echo '<div class="badge badge-warning">Upcoming</div>';
                                                } elseif ($row['Status'] == 1) {
                                                    echo '<div class="badge badge-success">Ongoing</div>';
                                                } elseif ($row['Status'] == 2) {
                                                    echo '<div class="badge badge-danger">Cancelled</div>';
                                                } elseif ($row['Status'] == 3) {
                                                    echo '<div class="badge badge-primary">Completed</div>';
                                                } elseif ($row['Status'] == 4) {
                                                    echo '<div class="badge badge-secondary">No-Show</div>';
                                                }                                                          
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAppointmentModal"
                                                    onclick="editAppointment('<?php echo $row['ID']; ?>', '<?php echo $row['AptNumber']; ?>', '<?php echo $row['Name']; ?>', 
                                                    '<?php echo $row['Email']; ?>', '<?php echo $row['PhoneNumber']; ?>', '<?php echo $row['AptDate']; ?>', '<?php echo $row['Services']; ?>', '<?php echo $row['Remark']; ?>', 
                                                    '<?php echo $row['Status']; ?>')">View</button>
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

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="vendors/select2/select2.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>

    <script>
    $(document).ready(function() {
        var table = new DataTable('#example', {
            order: [[0, 'asc']]
        });

        $('#searchDropdown').on('change', function() {
            var searchTerm = $(this).val();
            table.search(searchTerm).draw();
        });
    });

        function editAppointment(id, aptNumber, name, email, phoneNumber, aptDate, services, remark, status) {
        $('#editAppointmentId').val(id);
        $('#editAptNumber').val(aptNumber);
        $('#editName').val(name);
        $('#editEmail').val(email);
        $('#editPhoneNumber').val(phoneNumber);
        $('#editAptDate').val(aptDate);
        $('#editServices').val(services);
        $('#editRemark').val(remark);
        $('#editStatus').val(status);
    
    }
    </script>
</body>
</html>
