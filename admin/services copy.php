<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Insert new service
    if (isset($_POST['submit'])) {
        $sername = $_POST['sername'];
        $category = $_POST['category'];
        $cost = $_POST['cost'];
        $duration = $_POST['duration'];
        $description = $_POST['description']; // New field
        $maxAppointments = $_POST['maxAppointments']; // Get the max appointments field

        $query = mysqli_query($con, "INSERT INTO tblservices (ServiceName, CategoryName, Cost,Duration, ServiceDescription, MaxAppointmentsPerDay) VALUES ('$sername', '$category', '$cost', '$duration', '$description', '$maxAppointments')");
        
        if ($query) {
            echo "<script>alert('Service has been added.');</script>";
            echo "<script>window.location.href = 'services.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    

    // Update service
    if (isset($_POST['updateService'])) {
        $serid = $_POST['serid'];
        $sername = $_POST['sername'];
        $category = $_POST['category'];
        $cost = $_POST['cost'];
        $duration = $_POST['duration'];
        $description = $_POST['description']; // New field
        $maxAppointments = $_POST['maxAppointments']; // Get the max appointments field

        $query = mysqli_query($con, "UPDATE tblservices SET ServiceName='$sername', CategoryName='$category', Cost='$cost', Duration='$duration', ServiceDescription='$description', MaxAppointmentsPerDay='$maxAppointments' WHERE ID='$serid'");
        
        if ($query) {
            echo "<script>alert('Service has been updated.');</script>";
            echo "<script>window.location.href = 'services.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    

    // Delete service
    if (isset($_GET['delid'])) {
        $delid = $_GET['delid'];

        $query = mysqli_query($con, "DELETE FROM tblservices WHERE ID='$delid'");
        if ($query) {
            echo "<script>alert('Service has been deleted.');</script>";
            echo "<script>window.location.href = 'services.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Fetch categories for the form
    $categoryQuery = mysqli_query($con, "SELECT * FROM tblcategory");
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

                    <!-- Modal for Adding Services -->
                    <div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addServiceModalLabel">Add Services</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="services.php">
                                        <div class="form-group">
                                            <label for="serviceName">Name</label>
                                            <input type="text" class="form-control" id="serviceName" name="sername" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="serviceImage">Upload Image</label>
                                            <input type="file" class="form-control" id="serviceImage" name="serviceImage" accept="image/*" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="serviceDescription">Description</label>
                                            <textarea class="form-control" id="serviceDescription" name="description" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="serviceCategory">Category</label>
                                            <select id="category" name="category" class="form-control" required>
                                                <option value="">Select Category</option>
                                                <?php
                                                while ($row = mysqli_fetch_array($categoryQuery)) {
                                                    echo '<option value="' . $row['CategoryName'] . '">' . $row['CategoryName'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="serviceCost">Price</label>
                                            <input type="number" class="form-control" id="serviceCost" name="cost" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="serviceDuration">Duration (in minutes)</label>
                                            <input type="number" class="form-control" id="serviceDuration" name="duration" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="eMaxAppointments">Max Appointments Per Day</label>
                                            <input type="number" class="form-control" id="MaxAppointments" name="maxAppointments" required>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Editing Services -->
                    <div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="services.php" enctype="multipart/form-data">
                                        <input type="hidden" id="editServiceId" name="serid">
                                        <div class="form-group">
                                            <label for="editServiceName">Name</label>
                                            <input type="text" class="form-control" id="editServiceName" name="sername" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editServiceDescription">Description</label>
                                            <textarea class="form-control" id="editServiceDescription" name="description" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="editServiceCategory">Category</label>
                                            <select id="editCategory" name="category" class="form-control" required>
                                                <?php
                                                mysqli_data_seek($categoryQuery, 0);
                                                while ($row = mysqli_fetch_array($categoryQuery)) {
                                                    echo '<option value="' . $row['CategoryName'] . '">' . $row['CategoryName'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="editServiceCost">Price</label>
                                            <input type="number" class="form-control" id="editServiceCost" name="cost" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editServiceDuration">Duration (in minutes)</label>
                                            <input type="number" class="form-control" id="editServiceDuration" name="duration" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editMaxAppointments">Max Appointments Per Day</label>
                                            <input type="number" class="form-control" id="editMaxAppointments" name="maxAppointments" required>
                                        </div>
                                        <button type="submit" name="updateService" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Table -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Services List</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">Add Service</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="display expandable-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Description</th>
                                            <th>Category</th>
                                            <th>Duration</th>
                                            <th>Limit</th>
                                            <th>Creation Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch services
                                        $ret = mysqli_query($con, "SELECT * FROM tblservices");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                            ?>
                                            <tr>
                                                <th scope="row"><?php echo $cnt; ?></th>
                                                <td><?php echo htmlspecialchars($row['ServiceName']); ?></td>
                                                <td><?php echo htmlspecialchars($row['Cost']); ?></td>
                                                <td><?php echo htmlspecialchars($row['ServiceDescription']); ?></td>
                                                <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                                                <td><?php echo htmlspecialchars($row['Duration']); ?></td>
                                                <td><?php echo htmlspecialchars($row['MaxAppointmentsPerDay']); ?></td>
                                                <td><?php echo htmlspecialchars($row['CreationDate']); ?></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" 
                                                        onclick="editService(
                                                            '<?php echo $row['ID']; ?>', 
                                                            '<?php echo htmlspecialchars($row['ServiceName']); ?>', 
                                                            '<?php echo $row['Cost']; ?>', 
                                                            '<?php echo htmlspecialchars($row['CategoryName']); ?>', 
                                                            '<?php echo $row['MaxAppointmentsPerDay']; ?>', 
                                                            '<?php echo htmlspecialchars($row['ServiceDescription']); ?>', 
                                                            '<?php echo $row['Duration']; ?>'  // Include the Duration here
                                                        )">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="deleteService('<?php echo $row['ID']; ?>')">Delete</button>
                                                </td>
                                            </tr>
                                            <?php
                                            $cnt++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <?php include_once('partials/_footer.php'); ?>
            </div>
        </div>
    </div>
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
            new DataTable('#example');
        });
        // Edit Service Function 
        function editService(id, name, cost, category, maxAppointments, description, duration) {
            $('#editServiceId').val(id);
            $('#editServiceName').val(name);
            $('#editServiceCost').val(cost);
            $('#editCategory').val(category);
            $('#editMaxAppointments').val(maxAppointments);
            $('#editServiceDescription').val(description); // This line is already correct
            $('#editServiceDuration').val(duration); // Assign duration value to the duration field
            $('#editServiceModal').modal('show');
        }




        // Delete service confirmation
        function deleteService(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                window.location.href = 'services.php?delid=' + id;
            }
        }
    </script>
        <!-- Logout Confirmation Modal -->
        <?php include_once('partials/logout.php');?>

</body>

</html>
