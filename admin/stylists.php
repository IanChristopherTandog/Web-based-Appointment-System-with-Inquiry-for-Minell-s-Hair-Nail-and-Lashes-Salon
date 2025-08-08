<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Insert new stylist
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $specialty = implode(",", $_POST['specialty']);  // Convert array to comma-separated string


        // Insert into the database
        $query = mysqli_query($con, "INSERT INTO tblstylist (name, email, password, specialty) 
                                    VALUES ('$name', '$email', '$password', '$specialty')");

        // Check if the query was successful
        if ($query) {
            echo "<script>alert('Stylist has been added.');</script>";
            echo "<script>window.location.href = 'stylists.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    
    // Update stylist
    if (isset($_POST['updateStylist'])) {
        $stylistid = $_POST['stylistid'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $specialty = implode(",", $_POST['specialty']);  // Convert array to comma-separated string

        $query = mysqli_query($con, "UPDATE tblstylist SET name='$name', email='$email', specialty='$specialty' 
                                    WHERE id='$stylistid'");

        // Check if the update was successful
        if ($query) {
            echo "<script>alert('Stylist has been updated.');</script>";
            echo "<script>window.location.href = 'stylists.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    
    // Delete stylist
    if (isset($_GET['delid'])) {
        $delid = $_GET['delid'];

        $query = mysqli_query($con, "DELETE FROM tblstylist WHERE id='$delid'");
        if ($query) {
            echo "<script>alert('Stylist has been deleted.');</script>";
            echo "<script>window.location.href = 'stylists.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Handle password reset for a stylist
    if (isset($_GET['reset_password'])) {
        $stylistId = $_GET['reset_password'];

        // Set default password
        $defaultPassword = password_hash("123456", PASSWORD_DEFAULT);

        // Update the password in the database
        $query = mysqli_query($con, "UPDATE tblstylist SET password='$defaultPassword' WHERE id='$stylistId'");

        if ($query) {
            echo "<script>alert('Stylist password has been reset to 123456.');</script>";
            echo "<script>window.location.href = 'stylists.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    // Fetch stylists for the table
    $stylistQuery = mysqli_query($con, "SELECT * FROM tblstylist");
}
// Fetch specialties from tblcategory
$categoryQuery = mysqli_query($con, "SELECT * FROM tblcategory");
$categories = [];
while ($row = mysqli_fetch_assoc($categoryQuery)) {
    $categories[] = $row['CategoryName'];
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

                <!-- Modal for Adding Stylists -->
                <div class="modal fade" id="addStylistModal" tabindex="-1" role="dialog" aria-labelledby="addStylistModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addStylistModalLabel">Add Stylist</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="stylists.php">
                                    <div class="form-group">
                                        <label for="stylistName">Name</label>
                                        <input type="text" class="form-control" id="stylistName" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stylistEmail">Email</label>
                                        <input type="email" class="form-control" id="stylistEmail" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stylistPassword">Password</label>
                                        <input type="password" class="form-control" id="stylistPassword" name="password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stylistSpecialty">Specialty</label><br>
                                        <?php foreach ($categories as $category): ?>
                                            <input type="checkbox" name="specialty[]" value="<?php echo htmlentities($category); ?>"> <?php echo htmlentities($category); ?><br>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Editing Stylists -->
                <div class="modal fade" id="editStylistModal" tabindex="-1" role="dialog" aria-labelledby="editStylistModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editStylistModalLabel">Edit Stylist</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="stylists.php">
                                    <input type="hidden" id="editStylistId" name="stylistid">
                                    <div class="form-group">
                                        <label for="editStylistName">Name</label>
                                        <input type="text" class="form-control" id="editStylistName" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editStylistEmail">Email</label>
                                        <input type="email" class="form-control" id="editStylistEmail" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editStylistSpecialty">Specialty</label><br>
                                        <?php foreach ($categories as $category): ?>
                                            <input type="checkbox" id="editSpecialty<?php echo htmlentities($category); ?>" name="specialty[]" value="<?php echo htmlentities($category); ?>"> <?php echo htmlentities($category); ?><br>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="submit" name="updateStylist" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stylists Table -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Stylists List</h4>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addStylistModal">Add Stylist</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Specialty</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($stylistQuery)) {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row['name']); ?></td>
                                            <td><?php echo htmlentities($row['email']); ?></td>
                                            <td><?php echo htmlentities($row['created_at']); ?></td>
                                            <td><?php echo htmlentities($row['specialty']); ?></td> <!-- Comma-separated specialties -->
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                    data-toggle="modal" 
                                                    data-target="#editStylistModal" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlentities($row['name']); ?>"
                                                    data-email="<?php echo htmlentities($row['email']); ?>"
                                                    data-specialty="<?php echo htmlentities($row['specialty']); ?>">
                                                    Edit
                                                </button>
                                                <a href="stylists.php?delid=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete?');" class="btn btn-danger btn-sm">
                                                    Delete
                                                </a>
                                                <a href="stylists.php?reset_password=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to reset this stylist\'s password to 123456?');" class="btn btn-info btn-sm">
                                                    Reset Password
                                                </a>
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

            </div>
            <!-- content-wrapper ends -->
            <?php include('partials/_footer.php'); ?>
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div><!-- container-scroller -->


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
        
        // Event listener for edit buttons
        $(document).on('click', '.btn-warning', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const email = $(this).data('email');
            const specialty = $(this).data('specialty');

            editStylist(id, name, email, specialty); 
        });
    });

    // Edit Stylist Function 
    function editStylist(id, name, email, specialty) {
        $('#editStylistId').val(id);
        $('#editStylistName').val(name);
        $('#editStylistEmail').val(email);
        
        // Clear previous selections for specialties
        $('input[name="specialty[]"]').prop('checked', false);

        // Handle specialty checkboxes
        const specialties = specialty.split(",");
        specialties.forEach(function(value) {
            $('input[name="specialty[]"][value="' + value.trim() + '"]').prop('checked', true);
        });
    }
</script>

<?php include_once('partials/logout.php');?>

</body>
</html>