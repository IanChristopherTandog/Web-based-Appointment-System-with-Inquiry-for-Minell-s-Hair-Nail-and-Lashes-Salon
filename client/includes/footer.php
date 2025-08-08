<?php

$ret=mysqli_query($con,"select * from tblpage where PageType='contactus' ");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
  <div class="notification">
        This website is a project for educational and research purposes only.
      </div>
  <style>
    /* Notification at the bottom of the screen with blur effect */
    .notification {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: rgba(51, 51, 51, 0.7);
      color: white;
      text-align: center;
      padding: 10px 0;
      font-size: 14px;
      z-index: 9999;
      backdrop-filter: blur(5px); / Blur effect /
      -webkit-backdrop-filter: blur(5px); / For Safari */
    }
  </style>
<footer id="footer" class="footer accent-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">Minell's Salon</span>
          </a>
          <p>We pride ourselves on our high quality work and attention to detail. The products we use are of top quality branded products.</p>
          <div class="social-links d-flex mt-4">
            <a href="https://www.facebook.com/profile.php?id=100095092646978"><i class="bi bi-facebook"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#hero">Home</a></li>
            <li><a href="#about">About us</a></li>
            <li><a href="#portfolio">Services</a></li>
            <!-- <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li> -->
          </ul>
        </div>

        <!-- <div class="col-lg-2 col-6 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul> 
        </div> -->

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4><?php  echo $row['PageTitle'];?></h4>
          <p><strong>Address:</strong>  <?php  echo $row['address'];?>,</p>
          <p><strong>Phone:</strong> <span>+<?php  echo $row['MobileNumber'];?></span></p>
          <p><strong>Email:</strong> <span><?php  echo $row['Email'];?></span></p>
          <p><strong>Facebook:</strong> <span><a href="https://www.facebook.com/profile.php?id=100095092646978">Minell's Salon</a></span></p>
        </div>

      </div>
    </div>

  </footer>

  <?php } ?>