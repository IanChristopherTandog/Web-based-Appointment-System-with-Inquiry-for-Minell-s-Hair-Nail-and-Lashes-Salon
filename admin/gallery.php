<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
    exit;
}

// Function to rename the image
function renameImage($currentImagePath, $newFileName) {
    $fileExtension = pathinfo($currentImagePath, PATHINFO_EXTENSION);
    $newFilePath = "../uploads/" . $newFileName . "." . $fileExtension;
    return $newFilePath;
}

// Upload image
if (isset($_POST['upload_image'])) {
    $imageTmp = $_FILES['image']['tmp_name'];
    $pageType = isset($_POST['pageType']) ? $_POST['pageType'] : 'aboutus';  // Default to 'aboutus' if not set

    // Call the renameImage function to get the new file path
    $newFilePath = renameImage($_FILES['image']['name'], $pageType . "_" . time());

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($imageTmp, $newFilePath)) {
        $query = mysqli_query($con, "INSERT INTO tblimages (ImagePath, PageType) VALUES ('$newFilePath', '$pageType')");
        $msg = $query ? "Image uploaded successfully." : "Error uploading image. Please try again.";
    } else {
        $msg = "Failed to move uploaded file.";
    }
}


// Rename or replace image logic
if (isset($_POST['rename_replace_image'])) {
    $imageId = $_POST['imageId'];
    $newFileName = $_POST['newFileName'];
    $currentImagePath = $_POST['currentImagePath'];
    $newImageTmp = $_FILES['newImage']['tmp_name'];

    // Handle the new image upload if a new image is provided
    if (!empty($newImageTmp)) {
        $newFilePath = renameImage($currentImagePath, $newFileName);

        // Delete the old image
        if (file_exists($currentImagePath)) {
            unlink($currentImagePath);
        }

        // Move the new image to the desired directory
        if (move_uploaded_file($newImageTmp, $newFilePath)) {
            // Update the database with the new image path
            $updateQuery = mysqli_query($con, "UPDATE tblimages SET ImagePath='$newFilePath' WHERE id='$imageId'");
            $msg = $updateQuery ? "Image replaced and renamed successfully." : "Error updating the database.";
        } else {
            $msg = "Failed to move the new image.";
        }
    } else {
        // If no new image is provided, just rename the old image
        $newFilePath = renameImage($currentImagePath, $newFileName);
        if (rename($currentImagePath, $newFilePath)) {
            // Update the database with the new file path
            $updateQuery = mysqli_query($con, "UPDATE tblimages SET ImagePath='$newFilePath' WHERE id='$imageId'");
            $msg = $updateQuery ? "Image renamed successfully." : "Error updating the database.";
        } else {
            $msg = "Error renaming the file.";
        }
    }
}

// Delete image
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    
    // Get the image path to delete the file from the server
    $imageQuery = mysqli_query($con, "SELECT ImagePath FROM tblimages WHERE id='$deleteId'");
    $imageData = mysqli_fetch_assoc($imageQuery);
    $imagePath = $imageData['ImagePath'];

    // Delete from the database
    $deleteQuery = mysqli_query($con, "DELETE FROM tblimages WHERE id='$deleteId'");
    
    if ($deleteQuery) {
        // Delete the file from the server
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $msg = "Image deleted successfully.";
    } else {
        $msg = "Error deleting image.";
    }
}

// Fetch images for the selected page type
$pageType = isset($_GET['pageType']) ? $_GET['pageType'] : 'aboutus'; // Default to 'aboutus'
$images = mysqli_query($con, "SELECT * FROM tblimages");  // Fetch all images or filtered based on page type
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Minell's Admin - CMS</title>
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
    <div class="container-scroller">
        <?php include_once('partials/_navbar.php'); ?>

        <div class="container-fluid page-body-wrapper">
            
            <?php include_once('partials/_sidebar.php'); ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Upload Image</h4>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="image">Select Image</label>
                                            <input type="file" class="form-control" id="image" name="image" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="pageType">Select Page Type</label>
                                            <select id="pageType" class="form-control">
                                                <option value="aboutus" <?php echo ($pageType == 'aboutus') ? 'selected' : ''; ?>>About Us</option>
                                                <option value="logo" <?php echo ($pageType == 'logo') ? 'selected' : ''; ?>>Logo</option>
                                                <option value="promotion" <?php echo ($pageType == 'promotion') ? 'selected' : ''; ?>>Promotion</option>
                                                <!-- Add more options as needed -->
                                            </select>
                                        </div>

                                        <!-- Hidden input to pass pageType -->
                                        <input type="hidden" name="pageType" value="<?php echo $pageType; ?>">

                                        <button type="submit" name="upload_image" class="btn btn-primary mr-2">Upload</button>
                                    </form>

                                    <?php if (isset($msg)) echo "<div class='alert alert-info'>$msg</div>"; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Existing Images</h4>
                                    <div class="row">
                                        <?php while ($image = mysqli_fetch_assoc($images)) { ?>
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <img src="<?php echo $image['ImagePath']; ?>" class="card-img-top" alt="Image" style="max-height: 500px;">
                                                    <div class="card-body">
                                                        <p class="card-text"><?php echo basename($image['ImagePath']); ?></p>
                                                        <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#editModal-<?php echo $image['id']; ?>">Edit</a>
                                                        <a href="gallery.php?delete_id=<?php echo $image['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal-<?php echo $image['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-<?php echo $image['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="post" action="gallery.php" enctype="multipart/form-data">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel-<?php echo $image['id']; ?>">Rename or Replace Image</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="newFileName">New File Name</label>
                                                                    <input type="text" class="form-control" name="newFileName" required>
                                                                    <input type="hidden" name="imageId" value="<?php echo $image['id']; ?>">
                                                                    <input type="hidden" name="currentImagePath" value="<?php echo $image['ImagePath']; ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="newImage">Upload New Image (Optional)</label>
                                                                    <input type="file" class="form-control" name="newImage">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" name="rename_replace_image" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include_once('partials/_footer.php'); ?>
            </div>
        </div>
    </div>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/select2/select2.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>

    <?php include_once('partials/logout.php');?>
</body>
</html>
