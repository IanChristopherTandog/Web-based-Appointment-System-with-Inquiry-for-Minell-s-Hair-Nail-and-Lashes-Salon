<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    if (isset($_POST['submit'])) {
        $salondbaid = $_SESSION['salondbaid'];
        $pagetitle = mysqli_real_escape_string($con, $_POST['pagetitle']);
        $pagedes = mysqli_real_escape_string($con, $_POST['pagedes']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $mobnumber = mysqli_real_escape_string($con, $_POST['mobnumber']);
        $timing = mysqli_real_escape_string($con, $_POST['timing']);
        $address = mysqli_real_escape_string($con, $_POST['address']);

        // Update the SQL query to include Timing and Address
        $query = mysqli_query($con, "UPDATE tblpage SET 
            PageTitle='$pagetitle', 
            PageDescription='$pagedes', 
            Email='$email', 
            MobileNumber='$mobnumber',
            Timing='$timing',  
            Address='$address'  
            WHERE PageType='contactus'");

        if ($query) {
            $msg = "Contact Us has been updated.";
            echo "<script>var modalMessage = '$msg';</script>";
        } else {
            $msg = "Something Went Wrong. Please try again.";
            echo "<script>var modalMessage = '$msg';</script>";
        }
    }    

    $ret = mysqli_query($con, "SELECT * FROM tblpage WHERE PageType='contactus'");
    $row = mysqli_fetch_array($ret);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Minell's Admin - Contact Us</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.php -->
        <?php include_once('partials/_navbar.php');?>

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.php -->
            <?php include_once('partials/_settings-panel.php');?>
            <!-- partial -->
            <!-- partial:partials/_sidebar.php -->
            <?php include_once('partials/_sidebar.php');?>
            <!-- partial -->

            <div class="main-panel">        
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Contact Us</h4>
                                    <p class="card-description">Update Contact Us information</p>
                                    <form class="forms-sample" method="post">
                                        <div class="form-group">
                                            <label for="exampleInputName1">Page Title</label>
                                            <input type="text" class="form-control" id="exampleInputName1" name="pagetitle" value="<?php echo htmlspecialchars($row['PageTitle']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleTextarea1">Page Description</label>
                                            <textarea class="form-control" id="exampleTextarea1" name="pagedes" rows="4" required><?php echo htmlspecialchars($row['PageDescription']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputMobileNumber">Mobile Number</label>
                                            <input type="text" class="form-control" id="exampleInputMobileNumber" name="mobnumber" value="<?php echo htmlspecialchars($row['MobileNumber']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputTiming">Working Hours</label>
                                            <input type="text" class="form-control" id="exampleInputTiming" name="timing" value="<?php echo htmlspecialchars($row['Timing']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputAddress">Address</label>
                                            <input type="text" class="form-control" id="exampleInputAddress" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>
                                        </div>

                                    
                                        <button type="submit" name="submit" class="btn btn-primary mr-2">Update</button>
                                        <button class="btn btn-light" type="button" onclick="window.location.href='dashboard.php';">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- Modal -->
                <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="messageModalLabel">Message</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="modalMessageContent">
                                <!-- Message will be displayed here -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- partial:../../partials/_footer.html -->
                <?php include_once('partials/_footer.php');?>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- Logout Confirmation Modal -->
    <?php include_once('partials/logout.php');?>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="vendors/select2/select2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    <!-- End custom js for this page-->

    <script>
    $(document).ready(function() {
        if (typeof modalMessage !== 'undefined') {
            $('#modalMessageContent').text(modalMessage);
            $('#messageModal').modal('show');
        }
    });
    </script>

</body>

</html>
