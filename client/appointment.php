<?php 
include('includes/dbconnection.php');
session_start();
error_reporting(E_ALL);

function checkIfLoggedIn() {
  if (!isset($_SESSION['email'])) {
      // If the user is not logged in, redirect to login.php
      header('Location: ../admin/login.php');
      exit();
  }
}

checkIfLoggedIn();
// Assuming you store the user's email in the session when they log in
$email = $_SESSION['email'];

// Fetch the user's name and email from the database based on their email
$query = mysqli_query($con, "SELECT Name, Email FROM tbluser WHERE Email='$email'");
$row = mysqli_fetch_array($query);
$name = $row['Name'];
$email = $row['Email'];  // Optional, as it's already in session

if (isset($_POST['submit'])) {
    $services = $_POST['services'];
    $adate = date('Y-m-d', strtotime($_POST['adate']));
    $stylist = $_POST['stylist']; // Get the selected stylist
    $aptnumber = mt_rand(100000000, 999999999);

    // Fetch the MaxAppointmentsPerDay value from tblsettings
    $settingsQuery = mysqli_query($con, "SELECT MaxAppointmentsPerDay FROM tblsettings LIMIT 1");
    $settingsData = mysqli_fetch_array($settingsQuery);
    $maxAppointmentsPerDay = $settingsData['MaxAppointmentsPerDay'];

    // Check if the service has a maximum appointment limit per day
    $serviceQuery = mysqli_query($con, "SELECT MaxAppointmentsPerDay FROM tblservices WHERE ServiceName='$services'");
    $serviceData = mysqli_fetch_array($serviceQuery);
    $maxServiceAppointments = $serviceData['MaxAppointmentsPerDay'];

    // Count the number of appointments for this service on the selected date
    $serviceCountQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM tblappointment WHERE AptDate='$adate' AND Services='$services'");
    $serviceCountData = mysqli_fetch_array($serviceCountQuery);
    $currentServiceAppointments = $serviceCountData['total'];

    // Count the total number of appointments for the selected date (across all services)
    $totalCountQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM tblappointment WHERE AptDate='$adate'");
    $totalCountData = mysqli_fetch_array($totalCountQuery);
    $currentTotalAppointments = $totalCountData['total'];

    // Check if the service-specific limit has been reached
    if ($currentServiceAppointments >= $maxServiceAppointments) {
        $msg = "Sorry, the maximum number of appointments for this service on the selected date has been reached.";
    } 
    // Check if the total appointments for the day have reached the limit
    elseif ($currentTotalAppointments >= $maxAppointmentsPerDay) {
        $msg = "Sorry, the maximum number of appointments for this date has been reached.";
    } 
    else {
        // Get the current highest queue number for this selected date and chosen stylist
        $queueQuery = mysqli_query($con, "SELECT MAX(QueueNumber) AS maxQueue FROM tblappointment WHERE AptDate='$adate'");
        $queueData = mysqli_fetch_array($queueQuery);
        $nextQueueNumber = $queueData['maxQueue'] + 1; // Assign the next queue number

        // Insert appointment into the database, including the selected stylist and queue number
        $query = mysqli_query($con, "INSERT INTO tblappointment(AptNumber, Name, Email, AptDate, Services, Stylist, Status, QueueNumber) 
        VALUES('$aptnumber', '$name', '$email', '$adate', '$services', '$stylist', '0', '$nextQueueNumber')") or die(mysqli_error($con));

        if ($query) {
            $ret = mysqli_query($con, "SELECT AptNumber FROM tblappointment WHERE Email='$email'");
            $result = mysqli_fetch_array($ret);
            $_SESSION['aptno'] = $result['AptNumber'];
            echo "<script>window.location.href='thank-you.php'</script>";
        } else {
            $msg = "Something Went Wrong. Please try again.";
        }
    }
}
// Fetch FAQs from the database
$faqQuery = mysqli_query($con, "SELECT * FROM tblfaqs");

// Fetch categories
$category_query = "SELECT * FROM tblcategory";
$category_result = $con->query($category_query);

// Fetch services
$service_query = "SELECT * FROM tblservices";
$service_result = $con->query($service_query);
 
function slugify($text) {
  // Replace spaces and special characters
  return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $text));
}
// Fetch all day off events from the database
$query = "SELECT title, start_date AS start, end_date AS end FROM tbldayoffs";
$result = $con->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    // Format each event as required by FullCalendar
    $events[] = [
        'title' => $row['title'],
        'start' => $row['start'],
        'end' => $row['end'],
        'color' => '#ff4d4d', // Example color
        'allDay' => true // Set to true for all-day events
    ];
}

// Encode the events array as JSON for use in JavaScript
$events_json = json_encode($events);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Minell's Salon</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/minell-logo-nobg.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>


  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    /* Set calendar background color */
    .fc {
        background-color: white; /* Change background to white */
        color: black; /* Change text color to black for visibility */
    }

    /* Change text color for specific elements */
    .fc-event,
    .fc-daygrid-day-number {
        color: black; /* Set event and day numbers text color */
    }

    .fc-toolbar {
        background-color: white; /* Set the toolbar background to white */
        color: black; /* Set toolbar text color */
    }

    .fc-day-header {
        color: black; /* Set the day headers text color to black */
    }

    .fc-button {
        background-color: #444; /* Dark background for buttons */
        color: black; /* Button text color */
    }

    .fc-button:hover {
        background-color: #666; /* Lighter background on hover */
    }

    .past-date {
    background-color: #f8d7da; /* Light red background */
    color: #721c24; /* Dark red text */
    pointer-events: none; /* Prevent click events */
    position: relative; /* Ensure it can contain absolute elements */
    }

</style>



</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="homepage.php" class="logo d-flex align-items-center me-auto">
        <!-- <img src="assets/img/minell-logo-nobg.png" alt="Minnel's Salon"> -->
        <h1 class="sitename">Minell's Salon</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="homepage.php" class="active">HOME</a></li>
          <li><a href="#portfolio">SERVICES</a></li>
          <!-- <li><a href="#starter-section">BOOK NOW</a></li> -->
          <li><a href="#about">ABOUT</a></li>
          <li><a href="#contact">CONTACT</a></li>
          <li class="dropdown"><a href="#"><?php echo htmlspecialchars(strtoupper($name)); ?></span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="view-profile.php">VIEW PROFILE</a></li>
              <li><a href="client-inbox.php">MESSAGES</a></li>
              <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title accent-background">
      <div class="container position-relative">
        <h1>MAKE AN APPOINTMENT</h1>
        <p>Transform Your Look, One Appointment at a Time! Book Now for the Style You Deserve, with Convenient Scheduling and Personalized Care.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="homepage.php">Home</a></li>
            <li class="current">Make an appointment</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->
    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Book Your Appointment</h2>
            <p>Book an appointment with us for a personalized and exceptional salon experience.</p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up">
            <!-- Appointment Form -->
            <div class="container" data-aos="fade-up">
                <!-- Appointment Form -->
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6"> <!-- Left Column -->
                            <!-- Left Column: Form Group -->
                            <div class="form-group">
                                <label for="serviceType">Choose a Service Type</label>
                                <select class="form-control" name="serviceType" id="serviceType" required>
                                    <option value="">Select Service Type</option>
                                    <?php 
                                    $query = mysqli_query($con, "SELECT * FROM tblcategory");
                                    while ($row = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $row['CategoryName'] . '">' . $row['CategoryName'] . '</option>';
                                    } 
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="services">Choose a Service</label>
                                <select class="form-control" name="services" id="services" required>
                                    <option value="">Select a service type first</option>
                                </select>
                            </div>

                            <!-- Display Duration and Description below the service dropdown -->
                            <div id="serviceDetails" style="margin-top: 10px; display: none;">
                                <span id="serviceInfo"></span>
                            </div>

                            <div class="form-group">
                                <label for="adate">Appointment Date</label>
                                <input type="date" class="form-control" name="adate" id="adate" required readonly>
                            </div>

                            <!-- Calendar Modal -->
                            <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="calendarModalLabel">Select Appointment Date</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                        <div class="modal-body">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="stylist">Choose a Stylist</label>
                                <select class="form-control" name="stylist" id="stylist" required>
                                    <option value="">Select a service type first</option>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" name="submit" class="btn" style="background-color: #FFDF00; color: black;">Book Appointment</button>
                            </div>

                            <?php if (isset($msg)) { echo '<div class="alert alert-danger mt-3">'.$msg.'</div>'; } ?>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <!-- Card container for the image and description -->
                            <div class="card" style="max-width: 100%; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                                <!-- Card body -->
                                <div class="card-body d-flex flex-column align-items-center text-center">
                                    
                                    <!-- Service Image Placeholder -->
                                    <div class="col-12 d-flex justify-content-center align-items-center"> <!-- Added Flex Classes -->
                                        <a id="imageLink" href="../uploads/default-service.webp" class="glightbox">
                                            <img id="serviceImage" src="../uploads/default-service.webp" alt="Service Image" 
                                                style="max-width: 100%; height: 350px; margin-top: 10px; display: block;" 
                                                class="img-fluid" />
                                        </a>
                                    </div>

                                    <!-- Service Description below the image -->
                                    <div class="col-12" id="serviceDescription" style="margin-top: 10px; display: none;">
                                        <p></p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>


            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    // Set the minimum value of the date input to today's date
                    const today = new Date().toISOString().split('T')[0];
                    $('#adate').attr('min', today);

                    // Handle service type change
                    $('#serviceType').change(function() {
                        var serviceType = $(this).val();
                        if (serviceType) {
                            fetchServicesAndStylists(serviceType);
                            $('#services').html('<option value="">Select a Service</option>');
                        } else {
                            $('#stylist').html('<option value="">Select Stylist</option>');
                            $('#serviceDetails').hide();
                            $('#serviceInfo').text('');
                        }
                    });

                    // Handle service selection
                    $('#services').change(function() {
                        var selectedService = $(this).find(':selected');
                        if (selectedService.val() === "") {
                            return; 
                        }

                        var serviceCategory = selectedService.data('service-type');
                        $('#serviceType').val(serviceCategory);

                        const serviceDuration = selectedService.data('duration') || 0;
                        const serviceCost = selectedService.data('cost') || 0;
                        const serviceImage = selectedService.data('image') || '';
                        const serviceDescription = selectedService.data('description') || '';

                        $('#serviceInfo').text(`Duration: ${serviceDuration} mins | Price: ₱${serviceCost}`);
                        $('#serviceDetails').show();

                        const defaultImage = '../uploads/default-service.webp';
                        const imageToDisplay = serviceImage ? serviceImage : defaultImage;

                        $('#serviceImage').attr('src', imageToDisplay).show();
                        $('#serviceDescription').text(serviceDescription).show();

                        const selectedDate = $('#adate').val();
                        if (selectedDate) {
                            fetchAvailableTimes(selectedDate, serviceDuration);
                        }
                    });

                    // Function to fetch services and stylists based on selected service type
                    function fetchServicesAndStylists(serviceType) {
                        $.ajax({
                            type: 'POST',
                            url: 'fetch_data.php',
                            data: { serviceType: serviceType },
                            success: function(response) {
                                $('#stylist').html(response.stylists);
                                $('#services').html(response.services);
                                $('#serviceDetails').hide();
                                $('#serviceInfo').text('');

                                if (response.services.length > 0) {
                                    $('#services').trigger('change');
                                }
                            },
                            error: function() {
                                $('#services').html('<option value="">Error loading services</option>');
                                $('#stylist').html('<option value="">Error loading stylists</option>');
                            }
                        });
                    }

                    // Event listener for date change
                    $('#adate').change(function() {
                        const selectedDate = this.value;
                        const selectedService = $('#services').find(':selected');
                        const serviceDuration = selectedService.data('duration') || 0;
                        fetchAvailableTimes(selectedDate, serviceDuration);
                    });

                    // Function to fetch available times
                    function fetchAvailableTimes(date, duration) {
                        $.ajax({
                            type: 'POST',
                            url: 'fetch_times.php',
                            data: { adate: date, duration: duration },
                            success: function(response) {
                                $('#atime').html(response);
                            },
                            error: function() {
                                alert('Failed to load available times. Please try again.');
                            }
                        });
                    }

                    document.getElementById('adate').addEventListener('click', function() {
                        $('#calendarModal').modal('show');
                    });

                    $('#calendarModal').on('shown.bs.modal', function() {
                        const calendarEl = document.getElementById('calendar');

                        const calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            selectable: true,
                            validRange: {
                                start: (() => {
                                    const tomorrow = new Date();
                                    tomorrow.setDate(tomorrow.getDate());
                                    tomorrow.setHours(0, 0, 0, 0); // Ensure time is set to midnight
                                    return tomorrow;
                                })()
                            },
                            dateClick: function(info) {
                                const selectedDate = new Date(info.date);
                                const tomorrow = new Date();
                                tomorrow.setDate(tomorrow.getDate());
                                tomorrow.setHours(0, 0, 0, 0);

                                // Check if the clicked date has an event (within start and end dates)
                                const hasEvent = calendar.getEvents().some(event => {
                                    const eventStart = new Date(event.start);
                                    const eventEnd = new Date(event.end);
                                    
                                    // Check if the selected date is within the range, including the case where start and end are the same
                                    return selectedDate.getTime() === eventStart.getTime() || 
                                        (selectedDate >= eventStart && selectedDate < eventEnd);
                                });

                                // Prevent action if the date has an event
                                if (hasEvent) {
                                    return; // Do nothing if it's a date with an event
                                }

                                // Proceed only if the selected date is valid (not in the past)
                                if (selectedDate >= tomorrow) {
                                    document.getElementById('adate').value = info.dateStr;
                                    $('#calendarModal').modal('hide');

                                    const selectedService = $('#services').find(':selected');
                                    const serviceDuration = selectedService.data('duration') || 0;
                                    fetchAvailableTimes(info.dateStr, serviceDuration);
                                }
                            },
                            events: <?php echo $events_json; ?> // Use the events fetched from the database
                        });

                        calendar.render();
                    });

                    $('#calendarModal').on('hidden.bs.modal', function() {
                        const calendarEl = document.getElementById('calendar');
                        calendarEl.innerHTML = ''; // Clear the calendar when the modal is hidden
                    });

                });
            </script>
        </div>

    </section>

    <!-- Faq Section -->
    <section id="faq" class="faq section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Frequently Asked Questions</h2>
          <p>Welcome to our FAQ section! Here, we address some of the most common questions our clients have about our salon services. Whether you're a new visitor or a long-time customer, we aim to provide clarity and assistance. If you have any other questions not covered here, feel free to reach out to us directly!</p>

      </div><!-- End Section Title -->

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

            <div class="faq-container">

            <?php
          // Display FAQs dynamically
          while ($row = mysqli_fetch_array($faqQuery)) {
            ?>

              <div class="faq-item faq">
                <h3><?php echo $row['question']; ?></h3>
                <div class="faq-content">
                  <p><?php echo $row['answer']; ?></p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->
            <?php
                      }
                      ?>


            </div>

          </div><!-- End Faq Column-->

        </div>

      </div>

    </section><!-- /Faq Section -->

    <!-- Contact Section -->
     <?php include('includes/contact.php');?>

     


  </main>

  <?php include('includes/footer.php');?>
  

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

    <script>
        window.embeddedChatbotConfig = {
        chatbotId: "lZUIuzf4PutbIvCnUTmlM",
        domain: "www.chatbase.co"
        }
        </script>
        <script
        src="https://www.chatbase.co/embed.min.js"
        chatbotId="lZUIuzf4PutbIvCnUTmlM"
        domain="www.chatbase.co"
        defer>
    </script>
    
</body>

</html>