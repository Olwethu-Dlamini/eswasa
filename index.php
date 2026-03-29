<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';

// Fetch banners for slider
$banners = mysqli_query($conn, "SELECT * FROM banners");
if (!$banners) {
    die("Banner query failed: " . mysqli_error($conn));
}

// Fetch statistics - handle both old and new column structures
$stats = [];
$result = mysqli_query($conn, "SELECT * FROM site_statistics");
if (!$result) {
    die("Statistics query failed: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($result)) {
    // Handle missing columns gracefully
    $row['stat_key'] = $row['stat_key'] ?? ($row['stat_name'] ?? 'stat_' . $row['id']);
    $row['stat_label'] = $row['stat_label'] ?? ($row['stat_name'] ?? 'Statistic');
    $row['stat_value'] = $row['stat_value'] ?? 0;
    $stats[$row['stat_key']] = $row;
}

// Fetch events
$events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC LIMIT 3");
if (!$events) {
    die("Events query failed: " . mysqli_error($conn));
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ESWASA - Eswatini Standards Authority</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/odometer.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/tg-cursor.css">
    <link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css" media="screen">
    <link rel="stylesheet" type="text/css" href="assets/css/extralayers.css" media="screen">
    <link rel="stylesheet" href="assets/css/main.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
</head>
<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner">
            <div class="sk-dot1"></div><div class="sk-dot2"></div>
            <div class="rect3"></div><div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <!-- header-area -->
    <?php include("includes/header.php")?>
    <!-- main-area -->
    <main class="main-area fix">
   

<!-- Slider Area -->
<section class="slider-area">
    <div class="tp-banner-container">
        <div class="tp-banner">
            <ul>
                <?php
                if ($banners && mysqli_num_rows($banners) > 0) {
                     while($row = mysqli_fetch_assoc($banners)) {
                         $image_path_from_db = $row['file'] ?? '';
                         $display_path = '';

                         if (!empty($image_path_from_db)) {
                             if (strpos($image_path_from_db, 'admin/') === 0) {
                                 $display_path = $image_path_from_db;
                             } else if (strpos($image_path_from_db, 'uploads/') === 0) {
                                 $display_path = 'admin/' . $image_path_from_db;
                             } else {
                                 $display_path = 'admin/uploads/' . basename($image_path_from_db);
                             }
                         }
                ?>
                <li data-transition="slideright" data-slotamount="1" data-masterspeed="1000" data-delay="5000" data-saveperformance="off" data-title="Slide">
                    <!-- MAIN IMAGE -->
                    <?php if (!empty($display_path) && file_exists($display_path)): ?>
                        <img src="<?php echo htmlspecialchars($display_path); ?>"
                            alt="<?php echo htmlspecialchars($row['caption'] ?? 'Banner'); ?>"
                            data-bgposition="center center"
                            data-bgfit="cover"
                            data-bgrepeat="no-repeat">
                    <?php else: ?>
                        <img src="assets/img/slider/default-banner.jpg"
                            alt="<?php echo htmlspecialchars($row['caption'] ?? 'ESWASA Banner'); ?>"
                            data-bgposition="center center"
                            data-bgfit="cover"
                            data-bgrepeat="no-repeat">
                    <?php endif; ?>

                    <!-- OVERLAY LAYER -->
                    <div class="tp-caption tp-overlay-layer tp-resizeme"
                        data-x="center" data-hoffset="0"
                        data-y="center" data-voffset="0"
                        data-width="full"
                        data-height="full"
                        data-basealign="slide"
                        data-transform_idle="o:1;"
                        data-start="0"
                        style="z-index: 1;
                                background: linear-gradient(to top, rgba(0, 0, 0, 0.5), rgba(255, 255, 255, 0)); 
                                width: 100%;
                                height: 100%;
                                position: absolute;">
                    </div>

                    <!-- LAYER NR. 1 (Caption) -->
                    <div class="tp-caption sft sfb tp-resizeme rs-parallaxlevel-10"
                        data-x="left" data-hoffset="10"
                        data-y="center" data-voffset="-100"
                        data-speed="1000"
                        data-start="1000"
                        data-endspeed="1200"
                        data-easing="easeOutExpo"
                        data-elementdelay="0.01"
                        data-endelementdelay="0.1"
                        style="z-index: 5;">
                        <div><h2><?php echo htmlspecialchars($row['caption'] ?? 'ESWASA Banner'); ?></h2></div>
                    </div>

                    <!-- LAYER NR. 2 (Description) -->
                    <div class="tp-caption lfb ltt tp-resizeme rs-parallaxlevel-10"
                        data-x="left" data-hoffset="10"
                        data-y="center" data-voffset="20"
                        data-speed="1200"
                        data-start="1200"
                        data-endspeed="1200"
                        data-easing="easeOutExpo"
                        data-elementdelay="0.01"
                        data-endelementdelay="0.1"
                        style="z-index: 5;">
                        <div><p><?php echo htmlspecialchars($row['description'] ?? ''); ?></p></div>
                    </div>

                    <!-- LAYER NR. 3 (Button) -->
                    <div class="tp-caption lfb ltt tp-resizeme rs-parallaxlevel-10"
                        data-x="left" data-hoffset="10"
                        data-y="center" data-voffset="90"
                        data-speed="1400"
                        data-start="1400"
                        data-endspeed="1200"
                        data-easing="easeOutExpo"
                        data-elementdelay="0.01"
                        data-endelementdelay="0.1"
                        style="z-index: 5;">
                        <?php if (!empty($row['url'])) { ?>
                        <a href="<?php echo htmlspecialchars($row['url']); ?>" class="slider-btn slider-btn-1" target="_blank" rel="noopener">READ MORE</a>
                        <?php } ?>
                    </div>
                </li>
                <?php
                     }
                } else {
                    echo "<!-- No banners found in database -->\n";
                }
                ?>
            </ul>
            <div class="tp-bannertimer"></div>
        </div>
    </div>
</section>



<!-- course-area -->
<style>
    .coursesSlider .swiper-slide { height: auto; }
    .coursesSlider .swiper-slide .blog__post-item { height: 100%; }
    .coursesSlider .swiper-slide .blog__post-content { padding: 15px 20px 20px; }
    .coursesSlider .swiper-slide .blog__post-content .title { font-size: 18px; }
    .coursesSlider .swiper-slide .blog__post-content p { font-size: 13px; }
    .coursesSlider .swiper-slide .blog__post-content .cat img { width: 22px; }
    .coursesSlider .swiper-slide .blog__post-content .cat { font-size: 12px; padding: 4px 12px; }
</style>
<section class="courses-area bg-gray" style="padding-top: 15px; padding-bottom: 15px;">
    <div class="container">
        <div class="section__title-wrap mb-55">
            <div class="row align-items-center gap-4 gap-md-0">
                <div class="col-md-8">
                    <div class="section__title text-center text-md-start">
                        <h2 class="title tg-svg" style="color: #2e3191;">Discover</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Swiper -->
        <div class="swiper coursesSlider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="Certification.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Certification</b></a></h4>
                            <p>Certification to Management Systems and products. Let us assist you in demonstrating your organization's ability to meet requirements and needs.</p>
                            <a href="Certification.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="product.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Product Testing </b></a></h4>
                            <p>Food and product testing in microbiology. Testing performed in accordance to international standards. </p>
                            <a href="product.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="Standards.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Standards Development </b></a></h4>
                            <p>Bringing together different expertise and experiences, to develop mutually accepted solutions to common challenges. </p>
                            <a href="Standards.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Added Fourth Slide -->
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="training-about.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Training & Development </b></a></h4>
                            <p>Enhance your knowledge and skills with our specialized training programs, including Quality Management Systems Internal Auditing (SZNS ISO 19011:2018).</p>
                            <a href="training-about.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <!-- End of Added Fourth Slide -->

            </div>
            
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<!-- course-area-end -->

<!-- About Us Section -->
<section class="about-eswasa-area section-pt-120 section-pb-90">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <!-- Replace with your actual image path -->
                <div class="about-image">
                    <img src="admin/uploads/es.jpg" alt="ESWASA Building" class="img-fluid rounded shadow">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <div class="section__title mb-4">
                        <h2 class="title tg-svg" style="color: #2e3191;">About Us</h2>
                    </div>
                    <p>
                        The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023.
                    </p>
                    <p>
                        ESWASA is a national standards body mandated to develop, promote, and enforce standards and quality assurance in Eswatini.
                    </p>
                    <!-- Optional: Add a button -->
                    <!-- <a href="#" class="btn btn-primary mt-3">Learn More</a> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Section End -->


<!-- ESWASA Section -->
<section style="background:#e8e3f7; color:#000; padding:100px 0;">
    <div class="container">
        <div class="row align-items-center">
            <!-- Text – Left side -->
            <div class="col-lg-4 text-center text-lg-start">
                <h2 style="
                    font-size:2.9rem; 
                    font-weight:900; 
                    line-height:1.2; 
                    margin:0;
                    color: #000;
                ">
                    Trust the ESWASA<br>
                    Approved Quality Assurance<br>
                    Mark of Excellence
                </h2>
            </div>

            <!-- Photos – Right side -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Photo 1 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/management.png" 
                                 alt="ESWASA Certification" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Management Systems Certification Mark</div>
                    </div>
                    
                    <!-- Photo 2 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/product.png" 
                                 alt="ESWASA Standards" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Product Certification Mark</div>
                    </div>
                    
                    <!-- Photo 3 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/compulsory.png" 
                                 alt="ESWASA Testing" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Compulsory Standards Quality Mark</div>
                    </div>
                    
                    <!-- Photo 4 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/Ingelo.png" 
                                 alt="ESWASA Approved" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Ingelo Certification Scheme Mark</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End ESWASA Section -->

        
    
        </section>
        
        <!-- cta-area -->
        <section class="cta-area-three">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="cta__wrapper">
                            <div class="section__title white-title">
                                <h2 class="title tg-svg">Get Involved</h2>
                            </div>
                            <div class="cta__desc">
                                <p>Suggest a standard / Sign up to be a Technical Committee member / Get my organisation certified / Buy a Standard </p>
                            </div>
                            <div class="tg-button-wrap justify-content-center justify-content-md-end">
                                <a href="#" class="btn white-btn tg-svg"><span class="text">Learn More</span> <i class="fas fa-long-arrow-alt-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- cta-area-end -->


    </main>
    <!-- main-area-end -->
 <!-- Sticky Facebook Feed Toggle -->
<div class="sticky-wrapper">
  <div class="fb-sticky" onclick="toggleFeed()">
    <i class="fab fa-facebook-f" aria-hidden="true" style="rotate: 90deg;background-color: white;padding: 10px;border-radius: 50px;color: #3b5998;margin-bottom: 10px;"></i> Facebook Feed
  </div>
</div>

<!-- Facebook Feed Panel with Close Button OUTSIDE the iframe -->
<div class="fb-feed" id="fbFeed">
  <!-- Close Button Outside the iframe -->
  <div class="close-btn" onclick="toggleFeed()">&times;</div>

  <!-- Facebook Page Plugin -->
  <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v23.0&appId=395042390636886"></script>

<div class="fb-page" data-href="    https://www.facebook.com/eswasaupdates    " data-tabs="timeline" data-width="" data-height="900" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/eswasaupdates    " class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/eswasaupdates    ">Eswatini Standards Authority - SWASA</a></blockquote></div>

</div>

<style>
  .fb-feed {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    background-color: #fff0;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    z-index: 9999;
    display: flex;
    flex-direction: column;
  }

  .fb-feed.open {
    transform: translateX(0);
  }

  .close-btn {
    background-color: #2a3288;
    color: white;
    text-align: center;
    cursor: pointer;
    font-size: 24px;
    padding: 5px 20px;
    font-weight: bold;
    align-self: flex-end;
    z-index: 10000;
  }

.sticky-wrapper {
  position: fixed;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  z-index: 9998;
}

.fb-sticky { 
	background-color: #3b5998;
	color: white;
	padding: 10px;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 0px 10px 10px 0px;
	width: 50px;
	height: 200px;
	writing-mode: vertical-rl;
	text-orientation: mixed;
	font-weight: bold;
	text-align: center;
	rotate: -90deg;
}
</style>

<script>
  function toggleFeed() {
    const feed = document.getElementById('fbFeed');
    feed.classList.toggle('open');
  }
</script>

    <!-- footer-area -->
    <?php include("includes/footer.php")?>
    <!-- footer-area-end -->
    <!-- JS here -->
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.odometer.min.js"></script>
    <script src="assets/js/jquery.appear.js"></script>
    <script src="assets/js/tween-max.min.js"></script>
    <!-- SLIDER REVOLUTION 4.x SCRIPTS  -->
    <script src="rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
    <script src="rs-plugin/js/jquery.themepunch.plugins.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slick-animation.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/form-contact.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script>
        // Wait for DOM to load completely
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper after all other scripts have run
            setTimeout(function() {
                const swiper = new Swiper('.coursesSlider', {
                    // Optional parameters
                    loop: true,
                    
                    // If we need pagination
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    
                    // Navigation arrows
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    
                    // Number of slides per view
                    slidesPerView: 1,
                    spaceBetween: 30,
                    
                    // Responsive breakpoints
                    breakpoints: {
                        640: {
                            slidesPerView: 1,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 30,
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        },
                    },
                    
                    // Enable smooth transitions
                    speed: 800,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                });
            }, 100); // Small delay to ensure Revolution Slider is fully initialized
        });
    </script>
    
    <script>
            jQuery(document).ready(function() {         
                jQuery('.tp-banner').show().revolution(
                {
                    dottedOverlay:"none",
                    delay:5000,
                    startwidth:1170,
                    startheight:550,
                    hideThumbs:200,
                    thumbWidth:100,
                    thumbHeight:50,
                    thumbAmount:5,
                    navigationType:"bullet",
                    navigationArrows:"solo",
                    navigationStyle:"preview3",
                    touchenabled:"on",
                    onHoverStop:"on",
                    swipe_velocity: 0.7,
                    swipe_min_touches: 1,
                    swipe_max_touches: 1,
                    drag_block_vertical: false,
                    parallax:"mouse",
                    parallaxBgFreeze:"on",
                    parallaxLevels:[7,4,3,2,5,4,3,2,1,0],
                    keyboardNavigation:"off",
                    navigationHAlign:"center",
                    navigationVAlign:"bottom",
                    navigationHOffset:0,
                    navigationVOffset:20,
                    soloArrowLeftHalign:"left",
                    soloArrowLeftValign:"center",
                    soloArrowLeftHOffset:20,
                    soloArrowLeftVOffset:0,
                    soloArrowRightHalign:"right",
                    soloArrowRightValign:"center",
                    soloArrowRightHOffset:20,
                    soloArrowRightVOffset:0,
                    shadow:0,
                    fullWidth:"on",
                    fullScreen:"off",
                    spinner:"spinner4",
                    stopLoop:"off",
                    stopAfterLoops:-1,
                    stopAtSlide:-1,
                    shuffle:"off",
                    autoHeight:"off",                       
                    forceFullWidth:"off",                       
                    hideThumbsOnMobile:"off",
                    hideNavDelayOnMobile:1500,                      
                    hideBulletsOnMobile:"off",
                    hideArrowsOnMobile:"off",
                    hideThumbsUnderResolution:0,
                    hideSliderAtLimit:0,
                    hideCaptionAtLimit:0,
                    hideAllCaptionAtLilmit:0,
                    startWithSlide:0,
                    fullScreenOffsetContainer: ""   
                });             
            }); //ready
       </script>
</body>
</html>