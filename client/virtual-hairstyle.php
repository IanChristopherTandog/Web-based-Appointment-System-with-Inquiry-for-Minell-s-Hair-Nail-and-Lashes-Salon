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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    #starter-section body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: lightgray;
    }
    #starter-section .container {
        display: flex;
        align-items: flex-start;
        padding: 20px;
    }
    #starter-section .image-container {
        position: relative;
    }
    #starter-section .image-container img {
        width: 400px;
        height: auto;
        border-radius: 10px;
    }
    #starter-section .try-on-button {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #FFDF00;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    #starter-section .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }
    #starter-section .popup .styles {
        display: flex;
        justify-content: space-around;
    }
    #starter-section .popup .style-item {
        width: 150px;
        text-align: center;
        margin: 10px;
    }
    #starter-section .popup .style-item img {
        width: 120px;
        height: 120px;
        border-radius: 10px;
        border: 1px solid #ddd;
        cursor: pointer;
    }
    #starter-section .popup .style-item img.selected {
        border: 2px solid #FFDF00;
    }
    #starter-section .popup .style-item p {
        margin: 5px 0 0 0;
        font-size: 16px;
    }
    #starter-section .popup-close {
        background-color: #FFDF00;
        color: white;
        border: none;
        padding: 5px 10px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    #starter-section .style-selector {
        margin-left: 20px;
    }
    #starter-section .style-selector h2 {
        margin: 0;
        font-size: 24px;
    }
    #starter-section .styles {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    #starter-section .style-item {
        margin: 5px;
        text-align: center;
    }
    #starter-section .style-item img {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        border: 2px solid transparent;
        cursor: pointer;
    }
    #starter-section .style-item img.selected {
        border-color: #FFDF00;
    }
    #starter-section .style-item p {
        margin: 5px 0 0;
        font-size: 14px;
    }
    #starter-section .color-selector {
        margin-top: 20px;
    }
    #starter-section .color-selector h3 {
        margin: 0;
        font-size: 18px;
    }
    #starter-section .colors {
        display: flex;
        align-items: center;
        margin-top: 10px;
    }
    #starter-section .color-item {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin: 0 5px;
        cursor: pointer;
        border: 3px solid transparent;
    }
    #starter-section .color-item.selected {
        border-color: #FFDF00;
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
          <li><a href="#hero" class="active">HOME</a></li>
          <li><a href="homepage.php#about">ABOUT</a></li>
          <li><a href="homepage.php#portfolio">SERVICES</a></li>
          <li><a href="homepage.php#contact">CONTACT</a></li>
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

    <!-- Page Title -->
    <div class="page-title accent-background">
      <div class="container position-relative">
        <h1>Virtual Hairstyle</h1>
        <p>"Transform Your Look Instantly – Explore a World of Hairstyles with Our Virtual Tool! Find the perfect style that reflects your unique personality without the commitment."</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="homepage.php">Home</a></li>
            <li class="current">Virtual Hairstyle</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
      <div class="container">
        <div class="image-container">
            <img id="main-image" alt="Person with Bobcut 1 hairstyle" src="VR/page.png" />
            <button class="try-on-button" onclick="openPopup()">TRY ON</button>
        </div>
        <div class="style-selector">
            <h2>STYLE</h2>
            <div class="styles">
                <div class="style-item">
                    <img alt="Bobcut 1" class="selected" src="VR/picturesblack/Bobcut1black.jpg" onclick="changeStyle(this,'bobcut1')" />
                    <p>Bobcut 1</p>
                </div>
                <div class="style-item">
                    <img alt="Bobcut 2" src="VR/picturesblack/Bobcut2black.jpg" onclick="changeStyle(this, 'bobcut2')" />
                    <p>Bobcut 2</p>
                </div>
                <div class="style-item">
                    <img alt="Bobcut Wavy" src="VR/picturesblack/Bobcut Wavyblack.jpg" onclick="changeStyle(this, 'bobcutwavy')" />
                    <p>Bobcut Wavy</p>
                </div>
                <div class="style-item">
                    <img alt="Curly Bobcut 1" src="VR/picturesblack/Curly Bubcut 1black.jpg" onclick="changeStyle(this, 'curlybobcut1')" />
                    <p>Curly Bobcut 1</p>
                </div>
                <div class="style-item">
                    <img alt="Curly Bobcut 2" src="VR/picturesblack/Curly 2black.jpg" onclick="changeStyle(this, 'curlybobcut2')" />
                    <p>Curly Bobcut 2</p>
                </div>
                <div class="style-item">
                    <img alt="Combover" src="VR/picturesblack/Comboverblack.jpg" onclick="changeStyle(this, 'combover')" />
                    <p>Combover</p>
                </div>
                <div class="style-item">
                    <img alt="Short" src="VR/picturesblack/Shortblack.jpg" onclick="changeStyle(this, 'short')" />
                    <p>Short</p>
                </div>
                <div class="style-item">
                    <img alt="Pixie Cut 1" src="VR/picturesblack/Pixie Cut 1black.jpg" onclick="changeStyle(this, 'pixiecut1')" />
                    <p>Pixie Cut 1</p>
                </div>
                <div class="style-item">
                    <img alt="Pixie Cut 2" src="VR/picturesblack/Pixie Cut 2black.jpg" onclick="changeStyle(this, 'pixiecut2')" />
                    <p>Pixie Cut 2</p>
                </div>
                <div class="style-item">
                    <img alt="Updo" src="VR/picturesblack/Updoblack.jpg" onclick="changeStyle(this, 'updo')" />
                    <p>Updo</p>
                </div>
                <div class="style-item">
                    <img alt="Long Wavy" src="VR/picturesblack/Long Wavyblack.jpg" onclick="changeStyle(this, 'longwavy')" />
                    <p>Long Wavy</p>
                </div>
                <div class="style-item">
                    <img alt="Long Straight" src="VR/picturesblack/Long Straightblack.jpg" onclick="changeStyle(this, 'longstraight')" />
                    <p>Long Straight</p>
                </div>
                <div class="style-item">
                    <img alt="Curly 1" src="VR/picturesblack/Curly 1black.jpg" onclick="changeStyle(this, 'curly1')" />
                    <p>Curly 1</p>
                </div>
                <div class="style-item">
                    <img alt="Curly 2" src="VR/picturesblack/Curly 2black.jpg" onclick="changeStyle(this, 'curly2')" />
                    <p>Curly 2</p>
                </div>
                <div class="style-item">
                    <img alt="Coily" src="VR/picturesblack/Coilyblack.jpg" onclick="changeStyle(this, 'coily')" />
                    <p>Coily</p>    
                </div>
            </div>
            <div class="color-selector">
                <h3>COLOR</h3>
                <div class="colors">
                   <!--<div class="color-item"?> <img alt="x" src="reject.png" onclick="changeColor(this, 'default')"></div>-->
                   <div class="color-item" style="background-color: #000000;" onclick="changeColor(this, 'black')"></div>
                    <div class="color-item" style="background-color: #e76f0c;" onclick="changeColor(this, 'brown')"></div>
                    <div class="color-item" style="background-color: lightcyan;" onclick="changeColor(this, 'white')"></div>
                    <div class="color-item" style="background-color:  #FFF1DB;" onclick="changeColor(this, 'blonde')"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="popup" class="popup">
            <button class="popup-close" onclick="closePopup()">Close</button>
            <p>            choose a model</p>
            <div class="styles">
                <div class="style-item">
                    <img src="VR/model/first.jpg" alt="model" height="100" width="100" onclick="selectPopupImage(this.src)"/>
                    <p>model</p>
                </div>
            </div>
      </div><!-- End Section Title -->

    </section><!-- /Starter Section Section -->

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

  <script>
        const images = {
            "bobcut1": {
                "black": "VR/picturesblack/Bobcut1black.jpg",
                "brown": "VR/pictures/bobcut1.jpg",
                "white": "VR/pictureswhite/Bobcut 1white.jpg",
                "blonde": "VR/picturesblonde/Bobcut 1blonde.jpg"
            },
            "bobcut2": {
                "black": "VR/picturesblack/Bobcut2black.jpg",
                "brown": "VR/pictures/bobcut2.jpg",
                "white": "VR/pictureswhite/Bobcut 2white.jpg",
                "blonde": "VR/picturesblonde/Bobcut 2blonde.jpg"
            },
            "bobcutwavy": {
                "black": "VR/picturesblack/Bobcut Wavyblack.jpg",
                "brown": "VR/pictures/Bobcut Wavy.jpg",
                "white": "VR/pictureswhite/Bobcut Wavywhite.jpg",
                "blonde": "VR/picturesblonde/Bobcut Wavyblonde.jpg"
            },
            "curlybobcut1": {
                "black": "VR/picturesblack/Curly Bubcut 1black.jpg",
                "brown": "VR/pictures/Curly Bubcut 1.jpg",
                "white": "VR/pictureswhite/Curly Bubcut 1white.jpg",
                "blonde": "VR/picturesblonde/Curly Bubcut 1blonde.jpg"
            },
            "curlybobcut2": {
                "black": "VR/picturesblack/Curly Bubcut 2black.jpg",
                "brown": "VR/pictures/Curly Bubcut 2.jpg",
                "white": "VR/pictureswhite/Curly Bubcut 2white.jpg",
                "blonde": "VR/picturesblonde/Curly Bubcut 2blonde.jpg"
            },
            "combover": {
                "black": "VR/picturesblack/Comboverblack.jpg",
                "brown": "VR/pictures/Combover.jpg",
                "white": "VR/pictureswhite/Comboverwhite.jpg",
                "blonde": "VR/picturesblonde/Comboverblonde.jpg"
            },
            "short": {
                "black": "VR/picturesblack/Shortblack.jpg",
                "brown": "VR/pictures/Short.jpg",
                "white": "VR/pictureswhite/Shortwhite.jpg",
                "blonde": "VR/picturesblonde/Shortblonde.jpg"
            },
            "pixiecut1": {
                "black": "VR/picturesblack/Pixie Cut 1black.jpg",
                "brown": "VR/pictures/Pixie Cut 1.jpg",
                "white": "VR/pictureswhite/Pixie Cut 1white.jpg",
                "blonde": "VR/picturesblonde/Pixie Cut 1blonde.jpg"
            },
            "pixiecut2": {
                "black": "VR/picturesblack/Pixie Cut 2black.jpg",
                "brown": "VR/pictures/Pixie Cut 2.jpg",
                "white": "VR/pictureswhite/Pixie Cut 2white.jpg",
                "blonde": "VR/picturesblonde/Pixie Cut 2blonde.jpg"
            },
            "updo": {
                "black": "VR/picturesblack/Updoblack.jpg",
                "brown": "VR/pictures/Updo.jpg",
                "white": "VR/pictureswhite/Updowhite.jpg",
                "blonde": "VR/picturesblonde/Updoblonde.jpg"
            },
            "longwavy": {
                "black": "VR/picturesblack/Long Wavyblack.jpg",
                "brown": "VR/pictures/Long Wavy.jpg",
                "white": "VR/pictureswhite/Long Wavywhite.jpg",
                "blonde": "VR/picturesblonde/Long Wavyblonde.jpg"
            },
            "longstraight": {
                "black": "VR/picturesblack/Long Straightblack.jpg",
                "brown": "VR/pictures/Long Straight.jpg",
                "white": "VR/pictureswhite/Long Straightwhite.jpg",
                "blonde": "VR/picturesblonde/Long Straightblonde.jpg"
            },
            "curly1": {
                "black": "VR/picturesblack/Curly 1black.jpg",
                "brown": "VR/pictures/Curly 1.jpg",
                "white": "VR/pictureswhite/Curly 1white.jpg",
                "blonde": "VR/picturesblonde/Curly 1blonde.jpg"
            },
            "curly2": {
                "black": "VR/picturesblack/Curly 2black.jpg",
                "brown": "VR/pictures/Curly 2.jpg",
                "white": "VR/pictureswhite/Curly 2white.jpg",
                "blonde": "VR/picturesblonde/Curly 2blonde.jpg"
            },
            "coily": {
                "black": "VR/picturesblack/Coilyblack.jpg",
                "brown": "VR/pictures/Coily.jpg",
                "white": "VR/pictureswhite/Coilywhite.jpg",
                "blonde": "VR/picturesblonde/Coilyblonde.jpg"
            },
        };

        let currentColor = 'black';
        function changeStyle(element, style) {
            // Remove selected class from all style images
            const styleImages = document.querySelectorAll('.style-item img');
            styleImages.forEach(img => img.classList.remove('selected'));

            // Add selected class to the clicked image
            element.classList.add('selected');

            // Change the main image to the clicked image's src
            const mainImage = document.getElementById('main-image');
            mainImage.src = images[style][currentColor];
            mainImage.alt = style;
        }

        function changeColor(element, color) {
            // Remove selected class from all color items
            const colorItems = document.querySelectorAll('.color-item');
            colorItems.forEach(item => item.classList.remove('selected'));

            // Add selected class to the clicked color item
            element.classList.add('selected');

            // Change the current color
            currentColor = color;

            // Change the images to the selected color
            const styleImages = document.querySelectorAll('.style-item ');
            styleImages.forEach(img => {
                const style = img.alt.toLowerCase().replace(' ', '');
                img.src = images[style][currentColor];
            });

            // Change the main image to the selected color
            const mainImage = document.getElementById('main-image');
            const selectedStyle = document.querySelector('.style-item img.selected');
            if (selectedStyle) {
                const style = selectedStyle.alt.toLowerCase().replace(' ', '');
                mainImage.src = images[style][currentColor];
            }
        }
    </script>
    <!--try on button-->
    <script>
        function changeMainImage(src, element) {
            document.getElementById('main-image').src = src;
            var images = document.querySelectorAll('.style-item img');
            images.forEach(function(img) {
                img.classList.remove('selected');
            });
            element.classList.add('selected');
        }

        function openPopup() {
            document.getElementById('popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function selectPopupImage(src) {
            document.getElementById('main-image').src = src;
            closePopup();
        }
        function selectColor(element) {
            var options = document.querySelectorAll('.color-option');
            options.forEach(function(option) {
                option.classList.remove('selected');
            });
            element.classList.add('selected');
        }
    </script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
      window.embeddedChatbotConfig = {
      chatbotId: "sd6FoUlgpRjbJQJDuNyK0",
      domain: "www.chatbase.co"
      }
    </script>

    <script
      src="https://www.chatbase.co/embed.min.js"
      chatbotId="sd6FoUlgpRjbJQJDuNyK0"
      domain="www.chatbase.co"
      defer>
    </script>

</body>

</html>