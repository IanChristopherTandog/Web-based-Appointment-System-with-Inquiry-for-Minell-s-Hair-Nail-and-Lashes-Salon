<?php 
include('includes/dbconnection.php');
session_start();
error_reporting(0);

function checkIfLoggedIn() {
  if (!isset($_SESSION['email'])) {
      // If the user is not logged in, redirect to login.php
      header('Location: ../admin/login.php');
      exit();
  }
}

checkIfLoggedIn();

$email = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';


if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $services = $_POST['services'];
    $adate = $_POST['adate'];
    $atime = $_POST['atime'];
    $phone = $_POST['phone'];
    $aptnumber = mt_rand(100000000, 999999999);
    $query = mysqli_query($con, "INSERT INTO tblappointment(AptNumber, Name, Email, PhoneNumber, AptDate, AptTime, Services, Status) 
    VALUES('$aptnumber', '$name', '$email', '$phone', '$adate', '$atime', '$services', 1)");

    if ($query) {
        $ret = mysqli_query($con, "SELECT AptNumber FROM tblappointment WHERE Email='$email' AND PhoneNumber='$phone'");
        $result = mysqli_fetch_array($ret);
        $_SESSION['aptno'] = $result['AptNumber'];
        echo "<script>window.location.href='thank-you.php'</script>";	
    } else {
        $msg = "Something Went Wrong. Please try again";
    }
}
// Fetch FAQs from the database
$faqQuery = mysqli_query($con, "SELECT * FROM tblfaqs");

// Fetch categories
$category_query = "SELECT * FROM tblcategory ORDER BY CategoryName ASC";
$category_result = $con->query($category_query);

// Fetch services and sort alphabetically by ServiceName
$service_query = "SELECT * FROM tblservices ORDER BY ServiceName ASC";
$service_result = $con->query($service_query);


function slugify($text) {
  // Replace spaces and special characters
  return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $text));
}
function isLoggedIn() {
  return isset($_SESSION['email']); // Or use $_SESSION['name'] depending on your login system
}
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
  <link href="assets/img/minell-logo-nobg.png" rel="apple-touch-icon">

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

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    
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
          <li><a href="#hero" class="active">HOME</a></li>
          <li><a href="#about">ABOUT</a></li>
          <li><a href="#services">SERVICES</a></li>
          <li><a href="#faq">FAQs</a></li>
          <li><a href="#contact">CONTACT</a></li>
          <li class="dropdown"><a href="#"><?php echo htmlspecialchars(strtoupper($email)); ?></span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="view-profile.php"><i class="bi bi-person"></i>VIEW PROFILE</a></li>
              <li><a href="client-inbox.php"><i class="bi bi-envelope"></i>INBOX</a></li>
              <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i>LOGOUT</a></li>
            </ul>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section accent-background">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h1>WELCOME TO </h1>
            <h1>THE SALON</h1>
            <p>We pride ourselves on our high quality work and attention to detail. The products we use are of top quality branded products.</p>
            <div class="d-flex">
              <a href="appointment.php" class="btn-get-started">Make an Appointment</a>
            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2 hero-img">
          <a href="virtual-hairstyle.php">
            <img src="assets/img/virtual.png" class="img-fluid animated" alt="">
          </a>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <?php include('includes/about.php');?>
    
        <!-- Portfolio Section -->
        <section id="services" class="portfolio section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Services</h2>
            <p>Our Service Prices</p>
        </div>

        <div class="container">
            <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

                <!-- Portfolio Filters -->
                <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
                    <li data-filter="*" class="filter-active">All</li>
                    <?php
                    while ($category = $category_result->fetch_assoc()) {
                        $slug_category = slugify($category['CategoryName']); // Slugify the category name
                        echo '<li data-filter=".filter-' . $slug_category . '">' . $category['CategoryName'] . '</li>';
                    }
                    ?>
                </ul>

                <!-- Portfolio Items -->
                <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200"> 
                    <?php
                    while ($service = $service_result->fetch_assoc()) {
                        $slug_category = slugify($service['CategoryName']);
                        $imageSrc = !empty($service['ImagePath']) ? htmlspecialchars($service['ImagePath']) : '../uploads/default-service.webp';

                        echo '
                        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-' . $slug_category . '" style="display: flex; flex-direction: column; height: 500px;">
                            <div class="portfolio-content h-100">
                                <a href="' . $imageSrc . '" data-glightbox="service-' . $service['ServiceID'] . '" class="glightbox">
                                    <img src="' . $imageSrc . '" alt="' . htmlspecialchars($service['ServiceName']) . '" 
                                        style="width: 100%; height: auto; max-height: 300px; object-fit: cover;" class="img-fluid services-img">
                                </a>
                                <div class="portfolio-info" style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <!-- Promo image placed to the left of service name -->
                                        ';
                                        if ($service['Promo'] === 'yes') {
                                          echo '<img src="assets/img/promotion.png" alt="Promotion" style="max-width: 30px; height: auto;">';
                                          echo '<h4>' . htmlspecialchars($service['ServiceName']) . '</h4>';
                                          echo '</div>';
                                          echo '<p>' . htmlspecialchars($service['ServiceDescription']) . '</p>';
                                          echo '<p>Cost: <span style="text-decoration: line-through; color: red;">₱' . htmlspecialchars($service['Cost']) . '</span>';
                                      
                                          if (!empty($service['DiscountPrice'])) {
                                              // Calculate the discount percentage
                                              $originalPrice = $service['Cost'];
                                              $discountedPrice = $service['DiscountPrice'];
                                              $discountPercentage = round(((($originalPrice - $discountedPrice) / $originalPrice) * 100), 2);
                                      
                                              echo ' <span style="color: green;">₱' . htmlspecialchars($service['DiscountPrice']) . ' (' . $discountPercentage . '% off)</span>';
                                          }
                                      
                                          echo '</p>';                                      
                                      } else {
                                          echo '<h4>' . htmlspecialchars($service['ServiceName']) . '</h4>
                                                </div>
                                                <p>' . htmlspecialchars($service['ServiceDescription']) . '</p>
                                                <p>Cost: ₱' . htmlspecialchars($service['Cost']) . '</p>';
                                      }
                                      
                                      echo '<a href="' . (isLoggedIn() ? 'appointment.php' : '../admin/login.php') . '" class="btn" style="background-color: #FFDF00; color: #000;">Book Appointment</a>
                                            </div>
                                        </div>
                                      </div>';
                                      
                    }
                    ?>
                </div>

            </div>
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
    <!-- /Contact Section -->

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