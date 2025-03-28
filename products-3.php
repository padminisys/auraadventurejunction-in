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
?><!DOCTYPE html>
<html lang="zxx">


<!-- Mirrored from html.designingmedia.com/seaquest/activity.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 22 Aug 2024 12:47:35 GMT -->
<head>
    <title>AURA ADVENTURE JUNCTION LLP</title>
    
    <!-- ROCKET BOOM -->
    <meta name="Keywords" content="Boom Lift Rental, Boom Lift Hire, Boom Lift On Rent, service providers, consultants, consulting firms, New Delhi, बूम लिफ्ट रेंटल, New delhi India">
    <meta name="Description" content="Boom Lift Rental Providers in new delhi, बूम लिफ्ट रेंटल सर्विस प्रोवाइडर. Get contact details and address of Boom Lift Rental, Boom Lift Hire, Boom Lift On Rent firms and companies in new delhi">
    <!-- HIGH ROPE COURSE -->
    <meta name="keywords" content="High Rope Course manufacturer &amp; oem manufacturer, rope course, rope course, adventure &amp; trekking tours, Adventure 1 , Adventure 1 Zone map &amp; directions">
<!-- soap football -->
<meta name="keywords" content="Soapfootball Saket, Reviews, Contact number, Phone number, Address, Map, Soapfootball Delhi, Ratings, Directions, Official website link, Working hours, Services" >
<!-- INFLATABLE MELTDOWN -->
<meta property="description" content="Jumpking India, largest inflatable water slide &amp; bounce house supplier, wholesaler manufacturer. Fun commercial inflatable bounce house game Melt Down Inflatable">

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
                            <img src="assets/images/Products/21_Product_Rocket Boom/compressed_1726651566759.jpeg" alt="image" class="img-fluid">
                        </figure>
                        <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-21">
                            <figure class="activity-plus mb-0">
                                <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                            </figure>
                        </a>
                    </div>
							
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/22_Product_Inflatable_Swimming Pool/compressed_1726651560700.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-22">
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
                                    <img src="assets/images/Products/23_Product_High Rope Course/compressed_1726651564114.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-23">
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
                                    <img src="assets/images/Products/24_Product_Paint ball/compressed_1726651567662.jpeg" alt="blog-img" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-24">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
						
                           
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/25_Product_Soapy Football/compressed_1726651563761.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-25">
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
                            <img src="assets/images/Products/26_Product_Kids Pedal Boat/compressed_1726651561402.jpeg" alt="image" class="img-fluid">
                        </figure>
                        <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-26">
                            <figure class="activity-plus mb-0">
                                <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                            </figure>
                        </a>
                    </div>
							
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            <div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/27_Product_Inflatable Dart Game/compressed_1726651568583.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-27">
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
                                    <img src="assets/images/Products/28_Product_Inflatable Climbing Wall/compressed_1726651562657.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-28">
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
                                    <img src="assets/images/Products/29_Product_ Inflatable Melt Down/compressed_1726651564627.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-29">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/30_Product_Inflatable Advertisement Stand/compressed_1726651563688.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-30">
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
    <li><a href="products-2.php">2</a></li>
    <li><a href="#" class="active">3</a></li>
    <li><a href="products-4.php">4</a></li>
    <li><a href="products-5.php">5</a></li>
    
    <li><a href="products-4.php">»</a></li>
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
<div class="project_modal">
    <div id="blog-model-21" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                       <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/21_Product_Rocket Boom/compressed_1726651566759.jpeg" alt="blog-img" class="img-fluid">
								
                            </figure>
					  </div>
					  
                        <div class="project_content">
                            <h3>ROCKET BOOM </h3>
                            <span class="text">The Inflatable Rocket Boom offers an exhilarating adventure with its high-flying thrills. Participants are launched into the air, experiencing an exciting, bouncy ride that simulates rocket propulsion. Perfect for those seeking a fun, adrenaline-pumping inflatable attraction.</span>
                            
                            <span class="text">SIZE:- The size of an inflatable rocket boom can vary depending on the manufacturer and design. Typically, these inflatable structures range from around 10 to 30 feet in height. For specific dimensions, it's best to consult the product details from the manufacturer or supplier</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-22" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/22_Product_Inflatable_Swimming Pool/compressed_1726651566525.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/22_Product_Inflatable_Swimming Pool/compressed_1726651560700.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>INFLATABLE SWIMMING POOL </h3>
                            <span class="text">An inflatable swimming pool is a portable, easily set-up pool made from durable, flexible materials. Ideal for temporary use, it inflates quickly and offers a fun and convenient way to enjoy swimming in various outdoor settings or small spaces.
</span>
                           
                            <span class="text">SIZE:- Inflatable swimming pools come in various sizes, ranging from small, child-friendly versions of around 6-10 feet in diameter to larger family-sized options that can exceed 20 feet in length. The size you choose depends on your space and needs.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-23" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/23_Product_High Rope Course/compressed_1726651564114.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/23_Product_High Rope Course/compressed_1726651561109.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>HIGH ROPE COURSE </h3>
                            <span class="text">Our high rope course offers an exhilarating adventure, featuring elevated platforms and challenging obstacles that test balance, strength, and agility. Perfect for thrill-seekers, this aerial experience provides excitement, teamwork opportunities, and unforgettable views from above. Safety harnesses are included for all participants.</span>
                           
                            <span class="text">SIZE:-  Here are the typical sizes and specifications for various elements of a high rope course:Platform Height: Platforms are generally 5 to 15 meters (16 to 50 feet) above the ground. Rope Elements/Obstacles: Obstacles such as swinging logs, nets, and tightropes can range from 3 to 10 meters (10 to 33 feet) in length. Line Length: Zip lines often measure between 20 to 200 meters (65 to 656 feet), depending on the course design.Course Length: The total length of a high rope course varies but typically covers 50 to 300 meters (164 to 984 feet).Group Capacity: Each course section can generally accommodate 8-12 participants at a time, depending on the design.Safety Harness Capacity: The harnesses support up to 150 kg (330 lbs).Pole/Tree Distance: The distance between support poles or trees usually ranges from 8 to 15 meters (26 to 50 feet). These measurements can be adjusted depending on the specific course design and adventure level desired.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-24" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/24_Product_Paint ball/compressed_1726651567057.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/24_Product_Paint ball/compressed_1726651567662.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>PAINT BALL</h3>
                            <span class="text">Experience the ultimate thrill with our action-packed paintball arena. Strategize, team up, and engage in exhilarating battles across diverse terrains. Perfect for group events, team-building, or adrenaline-seekers, our paintball adventure guarantees fun, excitement, and unforgettable memories!

</span>
                           
                            <span class="text">SIZE:- Experience the ultimate thrill with our action-packed paintball arena, featuring high-quality Tippmann guns. Strategize and engage in exhilarating battles on diverse, large-scale fields. Made with durable, weather-resistant materials, our setup ensures safety, excitement, and unforgettable memories for all players!</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-25" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/25_Product_Soapy Football/compressed_1726651563761.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>SOAPY FOOTBALL  </h3>
                            <span class="text">Soapy football is a fun, thrilling twist on traditional football played on a slippery, soapy surface. Players slip, slide, and tackle their way to victory, creating a unique and hilarious experience that's perfect for team-building events, parties, and adventure seekers.
</span>
                          
                            <span class="text">SIZE:- Inflatable soapy football fields typically measure around 12 meters in length and 6 meters in width, with side walls and goals built into the structure. These inflatable boundaries help keep the water and soap contained, ensuring a safe and fun sliding experience for players.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-26" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/26_Product_Kids Pedal Boat/compressed_1726651561192.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/26_Product_Kids Pedal Boat/compressed_1726651561402.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>KIDS PEDAL BOAT </h3>
                            <span class="text">A kids' pedal boat is a small, child-friendly watercraft powered by foot pedals. Designed for safety and fun, it allows children to enjoy leisurely rides on calm waters while developing coordination and motor skills in a controlled, secure environment.

</span>
                           
                            <span class="text">SIZE:- Kids' pedal boats typically range in size from 4 to 6 feet in length and can seat 1 to 2 children. They are often made from durable, lightweight materials like polythene or fibreglass, which are UV-resistant and designed for long-lasting use in water environments.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-27" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/27_Product_Inflatable Dart Game/compressed_1726651568583.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/27_Product_Inflatable Dart Game/compressed_1726651569316.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>INFLATABLE DART GAME </h3>
                            <span class="text">Challenge your friends with our Inflatable Dart Game! Perfect for parties and events, this fun, safe game features a large, soft inflatable target and Velcro darts. Aim, throw, and score points while enjoying endless fun and friendly competition.
</span>
                           
                            <span class="text">SIZE:- The Inflatable Dart Game typically measures around 8 feet tall and 6 feet wide, offering a sizable target area for accurate throws and fun gameplay.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-28" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/28_Product_Inflatable Climbing Wall/compressed_1726651562285.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/28_Product_Inflatable Climbing Wall/compressed_1726651562657.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>INFLATABLE CLIMBING WALL</h3>
                            <span class="text">An inflatable climbing wall offers a fun, safe way to enjoy climbing activities. Designed with sturdy materials, it features multiple handholds and foot grips for all skill levels. Ideal for events and parties, it combines excitement with safety and ease of setup.
</span>
                           
                            <span class="text">SIZE:- Inflatable climbing walls typically range from 15 to 30 feet in height and 10 to 15 feet in width. The size can vary based on the specific design and purpose, with larger walls providing more climbing surface and challenge
</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-29" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/29_Product_ Inflatable Melt Down/compressed_1726651560362.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/29_Product_ Inflatable Melt Down/compressed_1726651564627.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>INFLATABLE MELTDOWN</h3>
                            <span class="text">In an inflatable meltdown, a sudden failure or burst of an inflatable structure causes chaos, leading to rapid deflation. This often results in damaged equipment, potential injuries, and disruption of activities, demanding swift response and safety measures.

</span>
                           
                            <span class="text">SIZE:- For an inflatable meltdown, size and diameter vary depending on the type of inflatable. For instance, an inflatable bounce house might be around 15-20 feet in diameter, while larger structures like obstacle courses or slides can exceed 30 feet in length or height. The exact dimensions depend on the specific inflatable involved.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-30" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/30_Product_Inflatable Advertisement Stand/compressed_1726651559706.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/30_Product_Inflatable Advertisement Stand/compressed_1726651563688.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>INFLATABLE ADVERTISEMENT STAND  </h3>
                            <span class="text">Boost your brand with our Inflatable Advertisement Stands! Custom-designed, eye-catching, and portable, they offer a unique way to showcase your business at events, promotions, and more. Stand out and attract attention effortlessly. Contact us today!
SIZE:- For inflatable advertisement stands, common sizes and heights vary depending on the design and purpose. Typically:
<div class="properties">
                                <ul class="list-unstyled mb-0">
                                    <li class="text-size-16"><i class="circle fa fa-check" aria-hidden="true"></i>Height:8 to 20 feet</li>
                                    <li class="text-size-16"><i class="circle fa fa-check" aria-hidden="true"></i>Width: 6 to 15 feet</li>
                                    <li class="text-size-16"><i class="circle fa fa-check" aria-hidden="true"></i>Depth: 6 to 12 feet</li>
                                </ul>
                            </div>



</span>
                           
                            <span class="text">Custom sizes can be made to fit specific needs.</span>
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
							<form id="enquiryForm" method="post" action="products-3.php">
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
