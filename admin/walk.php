<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Fetch services
    $servicesQuery = "SELECT ServiceName FROM tblservices";
    $servicesResult = mysqli_query($con, $servicesQuery);
    $services = [];
    while ($row = mysqli_fetch_assoc($servicesResult)) {
        $services[] = $row['ServiceName']; // Store only the ServiceName
    }


    // Fetch stylists
    $stylistsQuery = "SELECT name FROM tblstylist";
    $stylistsResult = mysqli_query($con, $stylistsQuery);
    $stylists = [];
    while ($row = mysqli_fetch_assoc($stylistsResult)) {
        $stylists[] = $row['name']; // Store only the stylist's name
    }

    // Function to generate a random 11-digit appointment number
    function generateAptNumber() {
        return str_pad(mt_rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
    }

    // Insert new appointment
    if (isset($_POST['addWalkinAppointment'])) {
        $aptNumber = generateAptNumber();
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $aptDate = $_POST['aptDate'];
        
        $serviceName = $_POST['services']; // This should be the selected service name
        $stylistName = $_POST['stylist']; // This should be the selected stylist name
        $status = 0; // Set status to Upcoming

        // Get the current highest queue number for this selected date
        $queueQuery = mysqli_query($con, "SELECT MAX(QueueNumber) AS maxQueue FROM tblappointment WHERE AptDate='$aptDate'");
        $queueData = mysqli_fetch_array($queueQuery);
        $nextQueueNumber = $queueData['maxQueue'] + 1; // Assign the next queue number

        // Insert appointment into the database
        $query = mysqli_query($con, "INSERT INTO tblappointment (AptNumber, Name, Email, PhoneNumber, AptDate, Services, Stylist, Type, Status, QueueNumber) VALUES ('$aptNumber', '$name', '$email', '$phoneNumber', '$aptDate', '$serviceName', '$stylistName', 'Walk-in', '$status', '$nextQueueNumber')");
        
        if ($query) {
            echo "<script>alert('Appointment has been added.');</script>";
            echo "<script>window.location.href = 'walk.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }



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

        $query = mysqli_query($con, "UPDATE tblappointment SET AptNumber='$aptNumber', Name='$name', Email='$email', PhoneNumber='$phoneNumber', AptDate='$aptDate', Services='$services', Remark='$remark', Status='$status' WHERE ID='$aptId'");
        if ($query) {
            echo "<script>alert('Appointment has been updated.');</script>";
            echo "<script>window.location.href = 'walk.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Delete appointment
    if (isset($_GET['delid'])) {
        $delid = intval($_GET['delid']);
        $query = mysqli_query($con, "DELETE FROM tblappointment WHERE ID='$delid'");
        if ($query) {
            echo "<script>alert('Appointment has been deleted.');</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Fetch appointments for the table
    $appointmentsQuery = mysqli_query($con, "SELECT * FROM tblappointment WHERE Type='Walk-in' ORDER BY ApplyDate DESC");

    // Fetch status counts
    $statusCountsQuery = mysqli_query($con, "SELECT Status, COUNT(*) as count FROM tblappointment GROUP BY Status");
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
                <!-- Modal for Adding Walk-in Appointments -->
                <!-- Modal for Adding Walk-in Appointments -->
                <div class="modal fade" id="addWalkinAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="addWalkinAppointmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addWalkinAppointmentModalLabel">Add Walk-in Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="walk.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name:</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Name:(Optional)">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email:</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="client@gmail.com(Optional)">
                                            </div>
                                            <!-- <div class="form-group">
                                                <label for="phoneNumber">Phone Number:</label>
                                                <input type="number" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="0912324:(Optional)">
                                            </div> -->

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="aptDate">Appointment Date:</label>
                                                <input type="date" class="form-control" id="aptDate" name="aptDate" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="services">Services:</label>
                                                <select class="form-control" id="services" name="services" required>
                                                    <option value="">Select a service</option>
                                                    <?php foreach ($services as $serviceName): ?>
                                                        <option value="<?php echo htmlspecialchars($serviceName); ?>"><?php echo htmlspecialchars($serviceName); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="stylist">Stylist:</label>
                                                <select class="form-control" id="stylist" name="stylist" required>
                                                    <option value="">Select a stylist</option>
                                                    <?php foreach ($stylists as $stylistName): ?>
                                                        <option value="<?php echo htmlspecialchars($stylistName); ?>"><?php echo htmlspecialchars($stylistName); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="appointmentType" value="Walk-in">
                                    <button type="submit" name="addWalkinAppointment" class="btn btn-primary">Add Appointment</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <form method="POST" action="walk.php">
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
                                            <!-- <div class="form-group">
                                                <label for="editPhoneNumber">Phone Number:</label>
                                                <input type="text" class="form-control" id="editPhoneNumber" name="phoneNumber" required readonly>
                                            </div> -->
                                        </div>

                                        <div class="col-md-6"> <!-- Right Column -->
                                            <div class="form-group">
                                                <label for="editAptDate">Appointment Date:</label>
                                                <input type="text" class="form-control" id="editAptDate" name="aptDate" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="editServices">Services:</label>
                                                <input type="text" class="form-control" id="editServices" name="services" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="editStylist">Stylist:</label>
                                                <input type="text" class="form-control" id="editStylist" name="stylist" required readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="editRemark">Remarks:</label>
                                        <textarea class="form-control" id="editRemark" name="remark"></textarea>
                                    </div>
                                    <button type="submit" name="updateAppointment" class="btn btn-primary mr-2">Update</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Table -->
                <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Walk-in Appointment List</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addWalkinAppointmentModal">
                                Add Walk-in Appointment
                            </button>
                        </div>
                        <div class="card-body"> 
                            <div class="table-responsive">
                                <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Walk-in Number</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Services</th>
                                        <th>Stylist</th>
                                        <th>Created Date</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($appointmentsQuery)) { ?>
                                        <tr>
                                            <td><?php echo $row['AptNumber']; ?></td>
                                            <td><?php echo $row['Name']; ?></td>
                                            <td><?php echo $row['Email']; ?>
                                            <td><?php echo $row['AptDate']; ?></td>
                                            <td><?php echo $row['Services']; ?></td>
                                            <td><?php echo $row['Stylist']; ?></td>
                                            <td><?php echo $row['ApplyDate']; ?></td>
                                            <td><?php echo $row['Remark']; ?></td>
                                            <td class="font-weight-medium">
                                                            <?php
                                                            // Example status logic
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
                                            <td><?php echo $row['Type']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editAppointmentModal"
                                                    onclick="editAppointment('<?php echo $row['ID']; ?>', '<?php echo $row['AptNumber']; ?>', '<?php echo $row['Name']; ?>', 
                                                    '<?php echo $row['Email']; ?>', '<?php echo $row['PhoneNumber']; ?>', '<?php echo $row['AptDate']; ?>', '<?php echo $row['Services']; ?>', '<?php echo $row['Stylist']; ?>', 
                                                    '<?php echo $row['Remark']; ?>', '<?php echo $row['Status']; ?>')">View</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>x<!-- End Table -->
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
            order: [[6, 'desc']]
        });

        $('#searchDropdown').on('change', function() {
            var searchTerm = $(this).val();
            table.search(searchTerm).draw();
        });
    });

    function editAppointment(id, aptNumber, name, email, phoneNumber, aptDate, services, stylist, remark, status) {
    $('#editAppointmentId').val(id);
    $('#editAptNumber').val(aptNumber);
    $('#editName').val(name);
    $('#editEmail').val(email);
    $('#editPhoneNumber').val(phoneNumber);
    $('#editAptDate').val(aptDate);
    $('#editServices').val(services);
    $('#editStylist').val(stylist); // Set the stylist value
    $('#editRemark').val(remark);
    $('#editStatus').val(status);
}
    </script>
</body>
</html>
