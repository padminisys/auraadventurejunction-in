<?php
session_start();

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include PHPMailer library via Composer's autoload
require 'vendor/autoload.php'; // Adjust path if necessary

// Initialize variables
$success = '';
$error = '';

// Get configuration variables
$recaptchaSecret = getenv('recaptchaSecret');
if (!$recaptchaSecret) {
	$error = 'Server configuration error: reCAPTCHA secret key is missing.';
}

$dataSiteKey = getenv('data_sitekey');
if (!$dataSiteKey) {
	$error = 'Server configuration error: reCAPTCHA site key is missing.';
}

$mailPassword = getenv('mail_password');
if (!$mailPassword) {
	$error = 'Server configuration error: Mail password is missing.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Validate CSRF Token
	if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		// Tokens do not match
		$error = 'Invalid CSRF token';
	} else {
		// Verify reCAPTCHA
		$response = $_POST['g-recaptcha-response'];
		$remoteIp = $_SERVER['REMOTE_ADDR'];

		$recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptchaResponse = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $response . '&remoteip=' . $remoteIp);
		$recaptchaData = json_decode($recaptchaResponse);

		if (!$recaptchaData->success) {
			$error = 'reCAPTCHA verification failed. Please try again.';
		} else {
			// Sanitize and validate input
			$installation = isset($_POST['installation']) ? 'Yes' : 'No';
			$option = filter_input(INPUT_POST, 'option', FILTER_SANITIZE_STRING);
			$name  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
			$number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
			$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
			$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
			$message   = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

			// Check for empty fields
			if (empty($name) || empty($number) || empty($email) || empty($location) || empty($message)) {
				$error = 'Please fill in all required fields.';
			} else {
				// Send email using PHPMailer
				$mail = new PHPMailer\PHPMailer\PHPMailer();
				$mail->isSMTP();
				$mail->Host       = getenv('mail_host');         // Replace with your SMTP host
				$mail->SMTPAuth   = true;
				$mail->Username   = getenv('mail_username');        // Replace with your SMTP username
				$mail->Password = getenv('mail_password');       // Replace with your SMTP password
				$mail->SMTPSecure = 'tls';
				$mail->Port       = 587;                          // Replace with your SMTP port

				$mail->setFrom('khushisoftwareindia@gmail.com', 'No-Reply');
				$mail->addAddress('auraadventure82@gmail.com', 'Dheeraj');      // Replace with your email address
				$mail->Subject = "Enquiry Form Submission from " . $name;
				$mail->Body    = "Installation: $installation\nOption: $option\nName: $name\nNumber: $number\nEmail: $email\nLocation: $location\nMessage:\n$message\n";

				if ($mail->send()) {
					$success = 'Thank you for contacting us. We will get back to you shortly.';
					// Optionally, reset CSRF token
					$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
				} else {
					$error = 'There was an error sending your message. Please try again later.';
				}
			}
		}
	}
}
?>


<!DOCTYPE html>
<html lang="zxx">


<!-- Mirrored from html.designingmedia.com/seaquest/activity.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 22 Aug 2024 12:47:35 GMT -->
<head>
    <title>AURA ADVENTURE JUNCTION LLP</title>
    
    <!-- Low Rope Course -->
    <meta name="description" content="Adventure 1 Zone offering Low Rope Course in Samalka ,New Delhi. Get best quote, read reviews, view photos and find contact details ">
    <meta name="keywords" content="Aura Adventure Junction, adventure activities in India, bungee jumping, zorbing, hot air balloon ride, rope course, trekking tours, adventure sports New Delhi, adventure company, inflatable games, team-building activities">

    <!-- rock climbing -->
    <meta name="description" content="Embark on an exhilarating wall climb adventure at aura adventure. Our Rock Climbing challenges are suitable for any age">
    <meta property="og:title" content="Trampoline Wall Climb - Aura adventure junction">
    <!-- Exciting Bull Ride -->
    <meta name="Title" content="Exciting Bull Ride Rentals In New Delhi | Mechanical Bull">
    <meta name="keywords" content="Bull Ride in New Delhi, Bull Ride for Birthday Party in New Delhi, Rent Bull Ride in New Delhi, Bull Ride Hire in New Delhi, Hire Bull Ride for Party New Delhi, Bull Ride for Rent, Bull Ride for Hire, New Delhi Bull Ride Services, Bull Ride Rental New Delhi">
<!-- burma bridge -->
<meta name="Keywords" content="Burma Bridge, manufacturers, suppliers, exporters, traders, dealers, manufacturing companies, retailers, producers, Adventure &amp; Trekking Tours India">
   <!-- atv bikes  -->
   <meta name="keywords" content="ATV in New DELHI, Veda Adventure Park, Off-Road Adventures,delhi  ATV Rides, Adventure Sports , Booking Inquiries">
    <!-- commando net -->
    <meta name="keywords" content="Commando Net manufacturer &amp; oem manufacturer, climbing net, rope course setup, adventure sporting &amp; trekking goods, India Adventures,  New Delhi, Delhi, India Adventures map &amp; directions">
    <!-- bumgee jumping -->

    
    
    
    <!-- /SEO Ultimate -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta charset="utf-8">
   <link rel="apple-touch-icon" sizes="57x57" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/images/favicon/logo.jpg">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon/logo.jpg">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/images/favicon/logo.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon/logo.jpg">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/images/favicon/logo.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon/logo.jpg">
    <link rel="manifest" href="assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../ms-icon-144x144.html">
    <meta name="theme-color" content="#ffffff">
    <!-- Latest compiled and minified CSS -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/js/bootstrap.min.js">
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="../../cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- StyleSheet link CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link href="assets/css/magnific-popup.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<a target="_blank" href="https://api.whatsapp.com/send?phone=919509924199&text=aura_adventure" class="whatsapp-button"><i class="fab fa-whatsapp"></i></a>
<!-- Back to top button -->
<a id="button"></a>
<div class="sub_banner position-relative">
    <header class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light p-0">
                <a class="navbar-brand" href="index.html">
                    <figure class="logo mb-0"><img src="assets/images/logo.jpg" alt="image" class="img-fluid"></figure>
                </a>
                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    <span class="navbar-toggler-icon"></span>
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                       
                        <li class="nav-item">
                            <a class="nav-link" href="index.html">Home</a>
                        </li>
						<li class="nav-item">
                            <a class="nav-link" href="about.html">About Us</a>
                        </li>
                        
						<li class="nav-item">
                            <a class="nav-link" href="products.php">Products</a>
                        </li>
                        
                       
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="last_list">
                <a class="search-box search" href="#search"><i class="fa-solid fa-magnifying-glass"></i></a>
                <a href="tel:+568925896325" class="text-decoration-none">
                    <div class="phone-number">
                        <figure class="banner-phoneicon mb-0">
                            <img src="assets/images/banner-phoneicon.png" alt="image" class="img-fluid">
                        </figure>
                        <span class="number">95099 24199</span>
                    </div>
                </a>
            </div>
        </div>
    </header>
    <!-- Search Form -->
    <div id="search" class=""> 
        <span class="close">X</span>
        <form role="search" id="searchform" method="get">
            <input value="" name="q" type="search" placeholder="Type to Search">
        </form>
    </div>
    <!-- Sub banner -->
	<?php if (!empty($success)): ?>
			<div class="alert alert-success">
				<?php echo htmlspecialchars($success); ?>
			</div>
	<?php elseif (!empty($error)): ?>
			<div class="alert alert-danger">
				<?php echo htmlspecialchars($error); ?>
			</div>
	<?php endif; ?>
  
</div>

<br>
<!-- Activity -->
<section class="activity3-con position-relative">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="activity_content text-center" data-aos="fade-up">
                   
                    <h2>Explore the excitement</h2>
                </div>
            </div>
        </div>
        <div class="activity_outer_box" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            
							 <div class="activity_wrapper position-relative">
                        <figure class="activity-image mb-0">
                            <img src="assets/images/Products/13_Product_Lowrope Course/compressed_1726651566907.jpeg" alt="image" class="img-fluid">
                        </figure>
                        <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-13">
                            <figure class="activity-plus mb-0">
                                <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                            </figure>
                        </a>
                    </div>
							
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/14_Product_Bungee Jump/compressed_1726651559348.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-14">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
						
                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
				
				 <div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/15_Product_Climbing Wall/compressed_1726651559917.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-15">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
				
                   
                </div>
                <div class="col-lg-3 col-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12" id="Zipline">
						
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/11_Product_Zipline/compressed_1726651561305.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-11">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
						
                           
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/12_Product_Zipcycle/compressed_1726651558323.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-12">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
						
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="activity_outer_box" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            
							 <div class="activity_wrapper position-relative">
                        <figure class="activity-image mb-0">
                            <img src="assets/images/Products/16_Product_Atv ride/compressed_1726651563094.jpeg" alt="image" class="img-fluid">
                        </figure>
                        <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-16">
                            <figure class="activity-plus mb-0">
                                <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                            </figure>
                        </a>
                    </div>
							
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            <div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/17_Product_Commando Nets/compressed_1726651559072.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-17">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                   
					<div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/18_Product_Machanical Bull Ride/compressed_1726651567883.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-18">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            <div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/19_Product_Burma Bridge/compressed_1726651557905.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-19">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/20_Product_Inflatable Running Bungee/compressed_1726651562368.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-20">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
						
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div id="wrapper">
<!-- start simple pagination -->
  
  <!--Active and Hoverable Pagination-->
   
   <!--Bordered Pagination-->
 
  <div class="b-pagination-outer">
 
  <ul id="border-pagination">
    <li><a class="" href="products.php">«</a></li>
    <li><a href="products.php">1</a></li>
    <li><a href="#" class="active">2</a></li>
    <li><a href="products-3.php">3</a></li>
    <li><a href="products-4.php">4</a></li>
    <li><a href="products-5.php">5</a></li>
    
    <li><a href="products-3.php">»</a></li>
  </ul> 
</div>
  
</div><!--wrapper-->
	  </div>
</section>
<!-- Testimonial -->

<!-- Footer -->
<section class="footer-con position-relative">
    <figure class="mb-0 footer-image">
        <img src="assets/images/footer.png" alt="image" class="img-fluid">
    </figure>
    <div class="container position-relative">
        <div class="middle_portion">
            <div class="row">
                <div class="col-xl-10 col-12 mx-auto">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="logo-content">
							<h6 class="heading">About us</h6>
                                
                                <p class="text text-size-16 mb-0">We are pleased to introduce our self as one of the most prominent names engaged in manufacturing & supplying of high-quality Inflatables like Advertising Sky Balloon, Inflatable Bouncer, Printed Balloon, Inflatable Zorbing Balls, Inflatable Arches, Advertising Back Pack Balloon, and Character Inflatables since 2006.</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                            <div class="links">
                                <h6 class="heading">Useful Links</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fa-solid fa-circle"></i><a href="#" class="text-decoration-none">Home</a></li>
                                    <li><i class="fa-solid fa-circle"></i><a href="#" class="text-decoration-none">About</a></li>
                                    <li><i class="fa-solid fa-circle"></i><a href="#" class="text-decoration-none">Services</a></li>
                                    <li><i class="fa-solid fa-circle"></i><a href="#" class="text-decoration-none">Blog</a></li>
                                    <li><i class="fa-solid fa-circle"></i><a href="#" class="text-decoration-none">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                            <div class="contact">
                                <h6 class="heading">Contact Info</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="text">
                                        <i class="fa-solid fa-phone"></i>
                                        <a href="tel:+91-9899753892" class="text-decoration-none">+91-9899753892, +91-9509924199</a>
                                    </li>
                                    <li class="text">
                                        <i class="fa-solid fa-envelope"></i>
                                        <a href="mailto:info@auraadventure.in" class="text-decoration-none">info@auraadventure.in</a>
										<a href="mailto:auraadventure82@gmail.com" class="text-decoration-none">auraadventure82@gmail.com</a>
                                    </li>
                                    <li class="text">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <p class="address mb-0">AURA ADVENTURE JUNCTION LLP 252, GOPAL JI COLONY, GALI NO.1,SAMALKA, NEW DELHI : 110037</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                            <div class="icon">
                                <h6 class="heading">Social Networks</h6>
                                <ul class="list-unstyled mb-0 social-icons">
                                    <li><a href="https://www.facebook.com/profile.php?id=61564764900354&mibextid=ZbWKwL" class="text-decoration-none"><i class="fa-brands fa-facebook-f social-networks" aria-hidden="true"></i></a></li>
									<li><a href="https://x.com/aura_adventure?t=vnor0jHpLWL81xzB7ult-g&s=09" class="text-decoration-none"><i class="fa-brands fa-x-twitter social-networks" aria-hidden="true"></i></a></li>
                                    <li><a href="https://youtube.com/@auraadventurejunction_in?si=I97Q9Y2tihNwPJxS" class="text-decoration-none"><i class="fa-brands fa-youtube social-networks" aria-hidden="true"></i></a></li>
                                    <li><a href="https://www.instagram.com/auraadventurejunctionllp?igsh=cnlmNnlzOWc4eTN4" class="text-decoration-none"><i class="fa-brands fa-instagram social-networks" aria-hidden="true"></i></a></li>
                                </ul><br>
								<a href="index.html" class="footer-logo" style="align-items:center">
                                    <figure class="mb-0"><img src="assets/images/footer.png" alt="image" ></figure>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <div class="copyright">
        <p class="mb-0 text-size-14">Copyright 2024, Aura Adventure LLP All Rights Reserved.</p>
    </div>
</section>
<!-- Project SECTION POPUP -->
<div class="project_modal" >
    <div id="blog-model-11" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                       <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/11_Product_Zipline/compressed_1726651561305.jpeg" alt="blog-img" class="img-fluid">
								
                            </figure>
					  </div>
					   <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/11_Product_Zipline/compressed_1726651561779.jpeg" alt="blog-img" class="img-fluid">
								
                            </figure>
					  </div>
                        <div class="project_content">
                            <h3>ZIPLINE </h3>
                            <span class="text">A zipline is an adventure activity where participants glide along a steel cable from a higher to a lower point, using a harness or pulley system. It offers a thrilling, fast-paced experience and breathtaking views of the surroundings.</span>
                            
                            <span class="text">SIZE:- The size of a zipline can vary widely. Typically, the cable length ranges from 100 to 1,500 meters, with heights varying from a few meters to over 100 meters above the ground. The overall setup size depends on the terrain and design.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-12" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/12_Product_Zipcycle/compressed_1726651558323.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/12_Product_Zipcycle/compressed_1726651560467.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>ZIPCYCLE </h3>
                            <span class="text">The Zipcycle is an exhilarating ride where participants pedal along a zipline, soaring above the ground and enjoying breathtaking views. Combining cycling with ziplining, it offers a unique and thrilling adventure experience in a safe, controlled environment.</span>
                           
                            <span class="text">SIZE:- The ZIPCYCLE typically measures about 2.5 to 3 meters in length, 1.2 meters in width, and 1.5 meters in height. Its dimensions can vary based on design and manufacturer, but it is generally compact to ensure a streamlined experience on the zipline.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-13" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/13_Product_Lowrope Course/compressed_1726651566907.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/13_Product_Lowrope Course/compressed_1726651568077.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>LOW ROPE COURSE </h3>
                            <span class="text">A low rope course is a ground-level obstacle course designed to promote teamwork, balance, and problem-solving. It features challenges like rope bridges, balance beams, and swinging ropes, usually set a few feet off the ground, ensuring safety while building confidence.</span>
                           
                            <span class="text">SIZE:- A low rope course typically spans 30 to 100 feet in length, with various elements spaced 3-4 feet off the ground. The ropes are made of durable, weather-resistant synthetic fibres or steel cables. Wooden beams, logs, and platforms are often used for support structures. Anchoring points are secured to trees or poles, with safety mats or mulch underneath for added protection.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-14" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/14_Product_Bungee Jump/compressed_1726651557149.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/14_Product_Bungee Jump/compressed_1726651559348.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>BUNGEE JUMP</h3>
                            <span class="text">Bungee jumping is an extreme sport where participants leap from a high platform, such as a bridge or crane, while attached to an elastic cord. The cord stretches and recoils, giving the jumper a thrilling free-fall experience before being pulled back.</span>
                           
                            <span class="text">SIZE:-Bungee jumping typically takes place from heights ranging between 50 to 250 meters (160 to 820 feet), depending on the location and the jump setup. However, some extreme bungee jumps can exceed 300 meters (1,000 feet) for a more intense experience.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-15" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/15_Product_Climbing Wall/compressed_1726651558781.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/15_Product_Climbing Wall/compressed_1726651559917.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>CLIMBING WALL </h3>
                            <span class="text">A climbing wall at Aura Adventure Junction would enhance your adventure offerings, attracting all ages. With varying difficulty levels, it caters to beginners and pros alike. Consider permanent installations or portable walls for events to expand your activities further.</span>
                          
                            <span class="text">SIZE:- Climbing walls come in various sizes, depending on the space and purpose. For outdoor or permanent installations, heights range from 20 to 40 feet (6 to 12 meters), while portable walls are typically around 24 feet (7 meters) tall. Widths can vary from 10 to 20 feet (3 to 6 meters), allowing for multiple climbing routes and difficulty levels.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-16" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/16_Product_Atv ride/compressed_1726651559129.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/16_Product_Atv ride/compressed_1726651563094.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>ATV RIDES  </h3>
                            <span class="text">ATV rides offer an exhilarating off-road experience, allowing riders to navigate rugged terrains, dirt paths, and steep hills. These all-terrain vehicles provide thrill-seekers with the excitement of speed and control, making them perfect for adventurous outdoor exploration and fun</span>
                           
                            <span class="text">SIZE:- 200 CC, 250 CC, 300 CC</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-17" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/17_Product_Commando Nets/compressed_1726651559072.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/17_Product_Commando Nets/compressed_1726651562869.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>COMMANDO NETS</h3>
                            <span class="text">Commando net is a high-intensity adventure activity involving a large net suspended at a height, where participants climb, crawl, and navigate through the ropes. It tests physical strength, balance, and agility, offering an exhilarating and challenging outdoor experience.</span>
                           
                            <span class="text">SIZE:- Commando net typically spans 10 to 20 meters in length and 3 to 5 meters in height, though sizes can vary based on the specific setup and difficulty level.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-18" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/18_Product_Machanical Bull Ride/compressed_1726651567883.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/18_Product_Machanical Bull Ride/compressed_1726651559026.jpEg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>MECHANICAL BULL RIDE</h3>
                            <span class="text">A Mechanical Bull Ride simulates the experience of riding a bucking bull. Participants straddle a rotating and tilting bull while trying to stay on as it moves erratically. It’s a fun, challenging ride that tests balance and strength.</span>
                           
                            <span class="text">SIZE:- Mechanical bulls typically measure around 6 to 10 feet in length, 3 to 5 feet in width, and about 4 to 6 feet in height, depending on the design. The ride area usually includes a large, padded arena for safety</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-19" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/19_Product_Burma Bridge/compressed_1726651557905.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>BURMA BRIDGE </h3>
                            <span class="text">The Burma Bridge is a thrilling outdoor challenge consisting of a suspended, narrow plank or rope bridge strung between two points. Participants walk across it while balancing, testing their agility and courage as they navigate the swaying bridge.</span>
                           
                            <span class="text">SIZE:-  The Burma Bridge typically spans around 10 to 15 meters (33 to 50 feet) in length and is about 1 meter (3.3 feet) wide. It is designed to be challenging yet manageable for participants of varying skill levels.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-20" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/20_Product_Inflatable Running Bungee/compressed_1726651562368.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>RUNNING BUNGEE  </h3>
                            <span class="text">Running a bungee involves participants wearing a harness attached to a bungee cord, allowing them to sprint or run while the cord creates resistance. The experience combines the thrill of bungee jumping with the excitement of high-speed running.</span>
                           
                            <span class="text">SIZE:- Running bungee setups typically require considerable space to ensure safety and effectiveness. The area needed depends on the cord's length and the height of the platform but generally includes a running track of at least 20-30 meters in length and sufficient clearance around the setup.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <div class="modal-body">
		<div class="modal-box">
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content clearfix">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						<div class="modal-body" style="border: 4px solid #11bef1; border-radius: 15px;">
							<h3 class="title">Enquiry Form</h3>
							<form id="enquiryForm" method="post" action="products-2.php">
								<!-- CSRF Token -->
								<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="installation" name="installation" value="1">
									<label class="form-check-label" for="installation">Installation</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" class="form-check-input" id="option_buy" name="option" value="Buy">
									<label class="form-check-label" for="option_buy">Buy</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" class="form-check-input" id="option_rent" name="option" value="Rent">
									<label class="form-check-label" for="option_rent">Rent</label>
								</div>

								<div class="form-group">
									<span class="input-icon"><i class="fa fa-user"></i></span>
									<input type="text" class="form-control" name="name" placeholder="Enter Name" required>
								</div>
								<div class="form-group">
									<span class="input-icon"><i class="fas fa-phone"></i></span>
									<input type="tel" class="form-control" name="number" placeholder="Enter Number" required>
								</div>

								<div class="form-group">
									<span class="input-icon"><i class="fas fa-envelope"></i></span>
									<input type="email" class="form-control" name="email" placeholder="Enter E-mail" required>
								</div>

								<div class="form-group">
									<span class="input-icon"><i class="fas fa-location"></i></span>
									<input type="text" class="form-control" name="location" placeholder="Enter Location" required>
								</div>

								<div class="form-group">
									<span class="input-icon"><i class="fas fa fa-comment"></i></span>
									<textarea class="form-control" name="message" rows="5" placeholder="Enter message" required></textarea>
								</div>

								<!-- Google reCAPTCHA widget -->
								<div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($dataSiteKey); ?>"></div> <!-- Replace with your Site Key -->

								<button type="submit" class="btn">Submit</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- PRE LOADER -->
<div class="loader-mask">
    <div class="loader">
        <div></div>
        <div></div>
    </div>
</div>
<!-- Latest compiled JavaScript -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/owl.carousel.js"></script>
<script src="assets/js/carousel.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/back-to-top-button.js"></script>
<script src="assets/js/preloader.js"></script>
<script src="assets/js/contact-form.js"></script>
<script src="assets/js/contact-validate.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/search.js"></script>
<script src="assets/js/popup-image.js"></script>
</body>

<!-- Mirrored from html.designingmedia.com/seaquest/activity.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 22 Aug 2024 12:47:35 GMT -->
</html>
