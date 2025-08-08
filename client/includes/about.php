
<!-- About Section -->
<?php
$ret = mysqli_query($con, "SELECT * FROM tblpage WHERE PageType='aboutus'");
$cnt = 1;
while ($row = mysqli_fetch_array($ret)) {
?>

<!-- About Section -->
<section id="about" class="about section">
    <div class="container">
        <div class="row gy-4">

            <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
                <?php 
                // Fetch a single image for the 'aboutus' page type from the database
                $aboutUsImages = mysqli_query($con, "SELECT ImagePath FROM tblimages WHERE PageType='aboutus' LIMIT 1");
                
                // Check if there is at least one image and display it
                if ($image = mysqli_fetch_assoc($aboutUsImages)) {
                    // Display the image without showing the filename
                    echo '<img src="' . $image['ImagePath'] . '" class="img-fluid" alt="Image" style="height: 424px; width: 1024px; object-fit: cover;">';
                }
                ?>
            </div>

            <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
                <h3><?php echo $row['PageTitle']; ?></h3>
                <br><br><br><br>
                <p class="fst-italic">
                    <?php echo $row['PageDescription']; ?>
                </p>
            </div>
        </div>
       
    </div>
</section><!-- /About Section -->

<!-- Mission Vision History Section -->
<section id="mission-vision-history" class="mission-vision-history section">
    <div class="container">
        <div class="row gy-4 justify-content-center">

            <!-- Mission -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="item text-center">
                    <div class="icon mb-3"><i class="bi bi-rocket" style="font-size: 2rem; color: #FFDF00;"></i></div>
                    <h2 class="title">Mission</h2>
                    <p class="description">
                        <?php echo $row['Mission']; ?>
                    </p>
                </div>
            </div><!-- End Mission -->

            <!-- Vision -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="item text-center">
                    <div class="icon mb-3"><i class="bi bi-eye" style="font-size: 2rem; color: #FFDF00;"></i></div>
                    <h2 class="title">Vision</h2>
                    <p class="description">
                        <?php echo $row['Vision']; ?>
                    </p>
                </div>
            </div><!-- End Vision -->

            <!-- History -->
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
                <div class="item text-center">
                    <div class="icon mb-3"><i class="bi bi-clock-history" style="font-size: 2rem; color: #FFDF00;"></i></div>
                    <h2 class="title">History</h2>
                    <p class="description">
                        <?php echo $row['History']; ?>
                    </p>
                </div>
            </div><!-- End History -->

        </div>
    </div>
</section>
<!-- Team Section -->
 <!-- 
<section id="team" class="team section">

<div class="container section-title text-center" data-aos="fade-up">
    <h2>Owners</h2>
    <p>At Minlle's Salon, our commitment to beauty and excellence is led by a passionate team dedicated to making every client feel their best. Meet the dynamic duo behind our success and the talented professionals who bring our vision to life.</p>
</div><

<div class="container">
    <div class="row gy-4 justify-content-center">

        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-1.jpg" class="img-fluid" alt="Mirasol Tañag" onerror="this.src='assets/img/default-profile.jpg'">

                </div>
                <div class="member-info">
                    <h4>Mirasol Tañag</h4>
                    <span>Founder & Creative Director</span>
                    <p>Mirasol's passion for beauty and artistry inspired the founding of Minlle's Salon. With her creative vision, she ensures that every client leaves the salon looking and feeling their best.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-2.jpg" class="img-fluid" alt="Reynell Tañag" onerror="this.src='assets/img/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h4>Reynell Tañag</h4>
                    <span>Co-Founder & Operations Manager</span>
                    <p>Reynell ensures the smooth operation of Minlle's Salon, overseeing day-to-day activities and making sure that the salon meets its high standards of service and quality.</p>
                </div>
            </div>
        </div>

    </div> 

    <div class="row gy-4 justify-content-center mt-4">

        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-3.jpg" class="img-fluid" alt="Maria Santos" onerror="this.src='assets/img/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h4>Maria Santos</h4>
                    <span>Senior Stylist</span>
                    <p>Maria brings over 10 years of hairstyling experience to the salon, specializing in cuts and color. Her dedication to perfection leaves every client with a style they love.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-4.jpg" class="img-fluid" alt="Anna Dela Cruz" onerror="this.src='assets/img/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h4>Anna Dela Cruz</h4>
                    <span>Hair & Makeup Artist</span>
                    <p>Anna is a makeup and hair expert known for her creative and versatile techniques. Her work graces fashion shows and bridal events, bringing beauty to every occasion.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="500">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-5.jpg" class="img-fluid" alt="John Cruz" onerror="this.src='assets/img/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h4>John Cruz</h4>
                    <span>Junior Stylist</span>
                    <p>John is an up-and-coming talent at Minlle's Salon, known for his fresh take on modern styles. He specializes in men's grooming and edgy cuts.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="600">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-6.jpg" class="img-fluid" alt="Jessica Reyes" onerror="this.src='assets/img/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h4>Jessica Reyes</h4>
                    <span>Nail Technician</span>
                    <p>Jessica specializes in intricate nail art and precision manicures. Her attention to detail and creativity make her a favorite among clients.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="700">
            <div class="team-member text-center">
                <div class="member-img">
                    <img src="assets/img/team/team-7.jpg" class="img-fluid" alt="Emily Garcia" onerror="this.src='assets/img/default-profile.jpg'">
                </div>
                <div class="member-info">
                    <h4>Emily Garcia</h4>
                    <span>Receptionist</span>
                    <p>Emily is the friendly face clients see when they walk into Minlle's Salon. Her welcoming personality and efficiency help keep everything running smoothly.</p>
                </div>
            </div>
        </div>

    </div> -->
</div>

</section><!-- /Team Section -->

<?php } ?>
