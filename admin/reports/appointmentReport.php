<?php
    $result = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get filter inputs
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $status = $_POST['status'];
        $type = $_POST['type'];
        $service = $_POST['service'];
        $stylist = $_POST['stylist']; // Add stylist filter

        // Base query
        $query = "SELECT * FROM tblappointment WHERE AptDate BETWEEN '$startDate' AND '$endDate'";

        if ($status !== '') { // Explicitly check against empty string
            $query .= " AND Status = '$status'";
        }
        if (!empty($type)) {
            $query .= " AND Type = '$type'";
        }
        if (!empty($service)) {
            $query .= " AND Services = '$service'";
        }
        if (!empty($stylist)) {  // Add the stylist filter to the query
            $query .= " AND Stylist = '$stylist'";
        }

        // Execute query
        $result = mysqli_query($con, $query);

        if (!$result) {
            die("Error executing query: " . mysqli_error($con));
        }
    }
?>

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Appointment Report Generation</h4>
    </div>
        <div class="card-body">
            <form method="POST" action="">
            <div class="row">
                <!-- Start Date -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="startDate" class="font-weight-bold">Start Date:</label>
                        <input type="date" id="startDate" name="startDate" class="form-control" value="<?php echo isset($startDate) ? $startDate : ''; ?>" required>
                    </div>
                </div>
                
                <!-- End Date -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="endDate" class="font-weight-bold">End Date:</label>
                        <input type="date" id="endDate" name="endDate" class="form-control" value="<?php echo isset($endDate) ? $endDate : ''; ?>" required>
                    </div>
                </div>

                <!-- Service -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="service" class="font-weight-bold">Service:</label>
                        <select id="service" name="service" class="form-control">
                            <option value="">All</option>
                            <?php
                                // Fetch services from the database
                                $serviceQuery = "SELECT ServiceName FROM tblservices";
                                $serviceResult = mysqli_query($con, $serviceQuery);

                                if ($serviceResult) {
                                    // Loop through the results and display each service
                                    while ($row = mysqli_fetch_assoc($serviceResult)) {
                                        $serviceName = $row['ServiceName'];
                                        // Check if the current service matches the selected service from POST data
                                        $selected = (isset($service) && $service == $serviceName) ? 'selected' : '';
                                        echo "<option value=\"$serviceName\" $selected>$serviceName</option>";
                                    }
                                } else {
                                    echo "<option value=''>No services available</option>";
                                }
                            ?>
                        </select>
                    </div>  
                </div>

                <!-- Stylist -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="stylist" class="font-weight-bold">Stylist:</label>
                        <select id="stylist" name="stylist" class="form-control">
                            <option value="">All</option>
                            <?php
                                // Fetch stylist names from the database
                                $stylistQuery = "SELECT name AS StylistName FROM tblstylist";
                                $stylistResult = mysqli_query($con, $stylistQuery);

                                if ($stylistResult) {
                                    // Loop through the results and display each stylist
                                    while ($row = mysqli_fetch_assoc($stylistResult)) {
                                        $stylistName = $row['StylistName'];
                                        // Check if the current stylist matches the selected stylist from POST data
                                        $selected = (isset($stylist) && $stylist == $stylistName) ? 'selected' : '';
                                        echo "<option value=\"$stylistName\" $selected>$stylistName</option>";
                                    }
                                } else {
                                    echo "<option value=''>No stylists available</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Type -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="type" class="font-weight-bold">Type:</label>
                        <select id="type" name="type" class="form-control">
                            <option value="">All</option>
                            <option value="Online" <?php echo (isset($type) && $type == 'Online') ? 'selected' : ''; ?>>Online</option>
                            <option value="Walk-in" <?php echo (isset($type) && $type == 'Walk-in') ? 'selected' : ''; ?>>Walk-in</option>
                        </select>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status" class="font-weight-bold">Status:</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">All</option>
                            <option value="0" <?php echo (isset($status) && $status == '0') ? 'selected' : ''; ?>>Upcoming</option>
                            <option value="1" <?php echo (isset($status) && $status == '1') ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="2" <?php echo (isset($status) && $status == '2') ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="3" <?php echo (isset($status) && $status == '3') ? 'selected' : ''; ?>>Completed</option>
                            <option value="4" <?php echo (isset($status) && $status == '4') ? 'selected' : ''; ?>>No-Show</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                </div>
            </div>
        </form>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive mt-4">
                <table id="example" class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Apt Number</th>
                            <th>Name</th>
                            <!-- <th>Email</th>
                            <th>Phone</th> -->
                            <th>Apt Date</th>
                            <th>Apt Time</th>
                            <th>Services</th>
                            <th>Stylist</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['ID']; ?></td>
                                <td><?php echo $row['AptNumber']; ?></td>
                                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                <!-- <td><?php echo htmlspecialchars($row['Email']); ?></td>
                                <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td> -->
                                <td><?php echo $row['AptDate']; ?></td>
                                <td><?php echo $row['AptTime']; ?></td>
                                <td><?php echo htmlspecialchars($row['Services']); ?></td>
                                <td><?php echo htmlspecialchars($row['Stylist']); ?></td>
                                <td><?php echo htmlspecialchars($row['Type']); ?></td>
                                <td><?php echo $row['Price']; ?></td>
                                <td>
                                    <?php
                                        // Determine badge class and status text based on Status value
                                        if ($row['Status'] == 0) {
                                            $statusClass = 'badge-warning';
                                            $statusText = 'Upcoming';
                                        } elseif ($row['Status'] == 1) {
                                            $statusClass = 'badge-success';
                                            $statusText = 'Ongoing';
                                        } elseif ($row['Status'] == 2) {
                                            $statusClass = 'badge-danger';
                                            $statusText = 'Cancelled';
                                        } elseif ($row['Status'] == 3) {
                                            $statusClass = 'badge-primary';
                                            $statusText = 'Completed';
                                        } elseif ($row['Status'] == 4) {
                                            $statusClass = 'badge-secondary';
                                            $statusText = 'No-Show';
                                        } else {
                                            $statusClass = 'badge-secondary';
                                            $statusText = 'Unknown';
                                        }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($statusText); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['Remark']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                    <th>ID</th>
                            <th>Apt Number</th>
                            <th>Name</th>
                            <!-- <th>Email</th>
                            <th>Phone</th> -->
                            <th>Apt Date</th>
                            <th>Apt Time</th>
                            <th>Services</th>
                            <th>Stylist</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <p class="alert alert-info mt-4">No appointments found for the selected criteria.</p>
        <?php endif; ?>
    </div>
</div>
