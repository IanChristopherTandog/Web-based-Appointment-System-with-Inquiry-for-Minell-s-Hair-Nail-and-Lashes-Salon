<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Insert new Category
    if (isset($_POST['submit'])) {
        $category = $_POST['category'];

        $query = mysqli_query($con, "INSERT INTO tblcategory(CategoryName) VALUE('$category')");
        if ($query) {
            echo "<script>alert('Category has been added.');</script>";
            echo "<script>window.location.href = 'categories.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    

    // Delete category
    if (isset($_GET['delid'])) {
        $delid = $_GET['delid'];

        $query = mysqli_query($con, "DELETE FROM tblcategory WHERE ID='$delid'");
        if ($query) {
            echo "<script>alert('category has been deleted.');</script>";
            echo "<script>window.location.href = 'categories.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Update category
    if (isset($_POST['updateCategory'])) {
        $caid = intval($_POST['caid']);
        $category = mysqli_real_escape_string($con, $_POST['category']);
    
        $stmt = $con->prepare("UPDATE tblcategory SET CategoryName = ? WHERE ID = ?");
        $stmt->bind_param("si", $category, $caid);
    
        if ($stmt->execute()) {
            echo "<script>alert('Category has been updated.');</script>";
            echo "<script>window.location.href = 'categories.php'</script>";
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

                    <!-- Modal for Adding Categories -->
                    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="categories.php">
                                        <div class="form-group">
                                            <label for="categoryName">Name</label>
                                            <input type="text" class="form-control" id="categoryName" name="category" required>
                                        </div>
                                        
                                        <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Editing Categories -->
                    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="categories.php">
                                        <input type="hidden" id="editCategoryId" name="caid">
                                        <div class="form-group">
                                            <label for="categoryName">Category Name</label>
                                            <input type="text" class="form-control" id="editCategoryName" name="category" required>
                                        </div>
                                        <button type="submit" name="updateCategory" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Category Table -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Category List</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">Add Category</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="display expandable-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = mysqli_query($con, "SELECT * FROM tblcategory");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                            ?>
                                            <tr>
                                                <th scope="row"><?php echo $cnt; ?></th>
                                                <td><?php echo $row['CategoryName']; ?></td>
                                                <td>
                                                    
                                                    <button class="btn btn-warning btn-sm" onclick="editCategory('<?php echo $row['ID']; ?>', '<?php echo $row['CategoryName']; ?>')">Edit</button>

                                                    <button class="btn btn-danger btn-sm" onclick="deleteCategory('<?php echo $row['ID']; ?>')">Delete</button>
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
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>

    <script>
        $(document).ready(function() {
            new DataTable('#example');
        });

    
        // Delete category confirmation
        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this Category?')) {
                window.location.href = 'categories.php?delid=' + id;
            }
        }
        function editCategory(id, categoryName) {
            $('#editCategoryId').val(id); // Set hidden input for category ID
            $('#editCategoryName').val(categoryName); // Set category name for editing
            $('#editCategoryModal').modal('show'); // Show the modal
        }
    </script>

<?php include_once('partials/logout.php');?>

</body>

</html>
