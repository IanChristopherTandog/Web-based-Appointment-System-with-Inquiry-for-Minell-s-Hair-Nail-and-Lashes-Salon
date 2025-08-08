<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Insert new user
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        $query = mysqli_query($con, "INSERT INTO tbluser (name, email, password) VALUES ('$name', '$email', '$password')");
        if ($query) {
            echo "<script>alert('User has been added.');</script>";
            echo "<script>window.location.href = 'clients.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Update user
    if (isset($_POST['updateUser'])) {
        $userid = $_POST['userid'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        // Password update logic can be added as needed

        $query = mysqli_query($con, "UPDATE tbluser SET name='$name', email='$email' WHERE id='$userid'");
        if ($query) {
            echo "<script>alert('User has been updated.');</script>";
            echo "<script>window.location.href = 'clients.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Delete user
    if (isset($_GET['delid'])) {
        $delid = $_GET['delid'];

        $query = mysqli_query($con, "DELETE FROM tbluser WHERE id='$delid'");
        if ($query) {
            echo "<script>alert('User has been deleted.');</script>";
            echo "<script>window.location.href = 'clients.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Fetch users for the table
    $userQuery = mysqli_query($con, "SELECT * FROM tbluser");
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

                <!-- Modal for Adding Users -->
                <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="clients.php">
                                    <div class="form-group">
                                        <label for="userName">Name</label>
                                        <input type="text" class="form-control" id="userName" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="userEmail">Email</label>
                                        <input type="email" class="form-control" id="userEmail" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="userPassword">Password</label>
                                        <input type="password" class="form-control" id="userPassword" name="password" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Editing Users -->
                <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="clients.php">
                                    <input type="hidden" id="editUserId" name="userid">
                                    <div class="form-group">
                                        <label for="editUserName">Name</label>
                                        <input type="text" class="form-control" id="editUserName" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editUserEmail">Email</label>
                                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                                    </div>
                                    <button type="submit" name="updateUser" class="btn btn-primary">Update</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Users List</h4>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Add User</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Verified</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($userQuery)) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo $cnt; ?></th>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $row['is_verified'] ? 'Yes' : 'No'; ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="editUser('<?php echo $row['id']; ?>', '<?php echo $row['name']; ?>', '<?php echo $row['email']; ?>')">Edit</button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteUser('<?php echo $row['id']; ?>')">Delete</button>
                                            </td>
                                        </tr>
                                        <?php
                                        $cnt++;
                                    } ?>
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
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>

    <script>
        $(document).ready(function() {
            new DataTable('#example');
        });

    // Edit User Function 
    function editUser(id, name, email) {
        $('#editUserId').val(id);
        $('#editUserName').val(name);
        $('#editUserEmail').val(email);
        $('#editUserModal').modal('show');
    }

    // Delete User Function
    function deleteUser(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = 'clients.php?delid=' + id;
        }
    }
</script>
<?php include_once('partials/logout.php');?>
</body>
</html>
