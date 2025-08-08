<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

if (strlen($_SESSION['salondbaid'] == 0)) {
    header('location:login.php');
} else {
    if (isset($_POST['submit_about'])) {
        // About Us update
        $salondbaid = $_SESSION['salondbaid'];
        $pagetitle = $_POST['pagetitle_about'];
        $pagedes = $_POST['pagedes_about'];
    
        // Prepare to update the page title and description
        $stmt = $con->prepare("UPDATE tblpage SET PageTitle=?, PageDescription=? WHERE PageType='aboutus'");
        $stmt->bind_param("ss", $pagetitle, $pagedes);
        
        // Check if a new image has been uploaded
        if (!empty($_FILES["aboutImage"]["name"])) { // Ensure this matches your input name
            // Handle the new image upload
            $targetDir = "../uploads/";
            $imageFileType = strtolower(pathinfo($_FILES["aboutImage"]["name"], PATHINFO_EXTENSION));
            $targetFile = $targetDir . uniqid() . '.' . $imageFileType; // Use a unique name
    
            // Validate the image
            $check = getimagesize($_FILES["aboutImage"]["tmp_name"]);
            if ($check === false || $_FILES["aboutImage"]["size"] > 2000000 || !in_array($imageFileType, ["jpg", "png", "jpeg"])) {
                echo "<script>alert('Invalid image file.');</script>";
                return;
            }
    
            // Upload the new file
            if (move_uploaded_file($_FILES["aboutImage"]["tmp_name"], $targetFile)) {
                // Update the page with the new image path
                $stmt = $con->prepare("UPDATE tblpage SET PageTitle=?, PageDescription=?, ImagePath=? WHERE PageType='aboutus'");
                $stmt->bind_param("sss", $pagetitle, $pagedes, $targetFile);
            } else {
                echo "<script>alert('Error uploading the new image.');</script>";
                return;
            }
        }
    
        // Execute the statement
        if ($stmt->execute()) {
            $msg = "About Us has been updated.";
            echo "<script>var modalMessage = '$msg';</script>";
        } else {
            $msg = "Something went wrong: " . mysqli_error($con);
            echo "<script>var modalMessage = '$msg';</script>";
        }
        
        $stmt->close();
    }
    

    if (isset($_POST['submit_contact'])) {
        // Contact Us update
        $salondbaid = $_SESSION['salondbaid'];
        $pagetitle = mysqli_real_escape_string($con, $_POST['pagetitle_contact']);
        $pagedes = mysqli_real_escape_string($con, $_POST['pagedes_contact']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $mobnumber = mysqli_real_escape_string($con, $_POST['mobnumber']);
        $timing = mysqli_real_escape_string($con, $_POST['timing']);
        $address = mysqli_real_escape_string($con, $_POST['address']);

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

    // Fetch About Us data
    $ret_about = mysqli_query($con, "SELECT * FROM tblpage WHERE PageType='aboutus'");
    $row_about = mysqli_fetch_array($ret_about);

    // Fetch Contact Us data
    $ret_contact = mysqli_query($con, "SELECT * FROM tblpage WHERE PageType='contactus'");
    $row_contact = mysqli_fetch_array($ret_contact);
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
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
</head>

<body>
    <div class="container-scroller">
        <?php include_once('partials/_navbar.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('partials/_sidebar.php'); ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <!-- About Us Form -->
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">About Us</h4>
                                    <form class="forms-sample" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="pagetitle_about">Page Title</label>
                                            <input type="text" class="form-control" id="pagetitle_about" name="pagetitle_about" value="<?php echo htmlspecialchars($row_about['PageTitle']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="pagedes_about">Page Description</label>
                                            <textarea class="form-control" id="pagedes_about" name="pagedes_about" rows="4" required><?php echo htmlspecialchars($row_about['PageDescription']); ?></textarea>
                                            <script>
                                                CKEDITOR.replace('pagedes_about');
                                            </script>
                                        </div>
                                        
                                        <button type="submit" name="submit_about" class="btn btn-primary mr-2">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Us Form -->
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Contact Us</h4>
                                    <form class="forms-sample" method="post">
                                        <div class="form-group">
                                            <label for="pagetitle_contact">Page Title</label>
                                            <input type="text" class="form-control" id="pagetitle_contact" name="pagetitle_contact" value="<?php echo htmlspecialchars($row_contact['PageTitle']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="pagedes_contact">Page Description</label>
                                            <textarea class="form-control" id="pagedes_contact" name="pagedes_contact" rows="4" required><?php echo htmlspecialchars($row_contact['PageDescription']); ?></textarea>
                                            <script>
                                                CKEDITOR.replace('pagedes_contact');
                                            </script>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row_contact['Email']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobnumber">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobnumber" name="mobnumber" value="<?php echo htmlspecialchars($row_contact['MobileNumber']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="timing">Working Hours</label>
                                            <input type="text" class="form-control" id="timing" name="timing" value="<?php echo htmlspecialchars($row_contact['Timing']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($row_contact['address']); ?>" required>
                                        </div>
                                        <button type="submit" name="submit_contact" class="btn btn-primary mr-2">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Modal -->
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

                <!-- Footer -->
                <?php include_once('partials/_footer.php'); ?>
            </div>
        </div>
    </div>

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

    <script>
        // Show message modal if there is a message
        window.onload = function() {
            var message = "<?php echo isset($msg) ? $msg : ''; ?>";
            if (message) {
                document.getElementById('modalMessageContent').innerText = message;
                $('#messageModal').modal('show');
            }
        };
    </script>
    <?php include_once('partials/logout.php');?>
</body>

</html>
