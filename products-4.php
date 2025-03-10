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
<a target="_blank" href="https://api.whatsapp.com/send?phone=9509924199&text=aura_adventure" class="whatsapp-button"><i class="fab fa-whatsapp"></i></a>
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
                            <img src="assets/images/Products/31_Product_Bungee Basket/compressed_1726651558129.jpeg" alt="image" class="img-fluid">
                        </figure>
                        <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-31">
                            <figure class="activity-plus mb-0">
                                <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                            </figure>
                        </a>
                    </div>
							
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/32_Product_Water walking Wall/1727348450851.jpg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-32">
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
                                    <img src="assets/images/Products/33_Product_Air Advertisment Balloon/compressed_1726651566978.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-33">
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
                                    <img src="assets/images/Products/34_Product_Inflatable Big Gloves Boxing/compressed_1726651561684.jpeg" alt="blog-img" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-34">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
						
                           
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/35_Product_Inflatable Sumo suit/compressed_1726651563600.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-35">
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
                            <img src="assets/images/Products/36_Product_Inflatable Tug-of-War/IMG_20240918_193850.jpg" alt="image" class="img-fluid">
                        </figure>
                        <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-36">
                            <figure class="activity-plus mb-0">
                                <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                            </figure>
                        </a>
                    </div>
							
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                            <div class="activity_wrapper position-relative">
                                <figure class="activity-image mb-0">
                                    <img src="assets/images/Products/37_Product_Gaint Swing/compressed_1726651566294.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-37">
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
                                    <img src="assets/images/Products/38_Product_Parasailing/compressed_1726651566431.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-38">
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
                                    <img src="assets/images/Products/39_Product_Bungee Tarampoline/compressed_1726651557149.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-39">
                                    <figure class="activity-plus mb-0">
                                        <img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
                                    </figure>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="activity_wrapper position-relative">
                                <figure class="activity-image">
                                    <img src="assets/images/Products/40_Product_Tree House/compressed_1726651556953.jpeg" alt="image" class="img-fluid">
                                </figure>
                                <a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-40">
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
    <li><a class="" href="products-3.php">«</a></li>
    <li><a href="products.php">1</a></li>
    <li><a href="products-2.php">2</a></li>
    <li><a href="products-3.php">3</a></li>
    <li><a href="#" class="active">4</a></li>
    <li><a href="products-5.php">5</a></li>
    
    <li><a href="products-5.php">»</a></li>
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
    <div id="blog-model-31" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                       <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/31_Product_Bungee Basket/compressed_1726651558129.jpeg" alt="blog-img" class="img-fluid">
								
                            </figure>
					  </div>
					   <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/31_Product_Bungee Basket/compressed_1726651559838.jpeg" alt="blog-img" class="img-fluid">
								
                            </figure>
					  </div>
					  
                        <div class="project_content">
                            <h3>BUNGEE BASKET </h3>
                            <span class="text">Experience the thrill of Bungee Basket at Aura Adventure Junction! Soar high and feel the rush as you bounce in our secure basket, combining the excitement of bungee jumping with a unique, exhilarating aerial adventure. Perfect for adrenaline seekers!

</span>
                            
                            <span class="text">SIZE:- The Bungee Basket at Aura Adventure Junction accommodates up to 4 people and has a spacious design, ensuring comfort and safety while you enjoy the thrilling experience of bouncing and soaring high above the ground.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-32" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/32_Product_Water walking Wall/1727348450851.jpg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/32_Product_Water walking Wall/1727348569174.jpg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>THE WATER WALKING BALL   </h3>
                            <span class="text">THE WATER WALKING BALL  is a transparent, inflatable sphere that allows users to walk or run on the water's surface. Ideal for fun and fitness, it provides a unique, exhilarating experience as you glide effortlessly across ponds, pools, or lakes.

</span>
                           
                            <span class="text">SIZE:- Water walking balls typically come in various sizes, ranging from 1.8 to 3 meters in diameter. This allows for different capacities, generally accommodating one or two people at a time.
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
    <div id="blog-model-33" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/33_Product_Air Advertisment Balloon/compressed_1726651559760.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/33_Product_Air Advertisment Balloon/compressed_1726651566978.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>AIR ADVERTISMENT BALLOON</h3>
                            <span class="text">An air advertisement balloon is a large, floating balloon with custom graphics or messages used for promotional purposes. Filled with helium or hot air, it hovers above events or locations, drawing attention and enhancing brand visibility.
</span>
                           
                            <span class="text">SIZE:- Air advertisement balloons typically range from 10 to 30 feet in height, though larger sizes are available for high-impact visibility. The exact size can be customized based on the specific advertising needs and location.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-34" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                       
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/34_Product_Inflatable Big Gloves Boxing/compressed_1726651561684.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>BIG GLOVE BOXING</h3>
                            <span class="text">Big glove boxing is a fun, high-energy sport where participants wear oversized, padded gloves. The large gloves reduce the risk of injury, making the game more accessible and entertaining. It's perfect for casual, non-competitive bouts and group activities.</span>
                           
                            <span class="text">SIZE:- Big glove boxing typically uses gloves that are 16 to 24 ounces, significantly larger than standard boxing gloves. The increased size enhances safety and cushioning, making the sport more suitable for casual play and reducing the impact on participants.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="project_modal">
    <div id="blog-model-35" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/35_Product_Inflatable Sumo suit/compressed_1726651560264.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/35_Product_Inflatable Sumo suit/compressed_1726651563600.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>INFLATABLE SUMO SUIT </h3>
                            <span class="text">The Inflatable Sumo Suit is a fun, durable costume that inflates to create a comical sumo wrestler look. Ideal for events, it allows participants to engage in playful wrestling matches, featuring ample padding for safety and ease of movement.</span>
                          
                            <span class="text">SIZE:- The inflatable Sumo Suit typically comes in one size that fits most adults, with adjustable straps or elastic sections to accommodate various body sizes. It's designed to be roomy enough for comfortable movement while ensuring a snug, secure fit.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-36" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/36_Product_Inflatable Tug-of-War/IMG_20240918_193850.jpg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
						
                            <h3>INFLATABLE TUG-OF-WAR </h3>
							
                            <span class="text">Inflatable tug of war is a fun, competitive game where teams pull on opposite ends of a large, bouncy rope across a cushioned, inflatable platform. The aim is to drag the opposing team past a designated line. Perfect for events and parties!</span>
                           
                            <span class="text">SIZE:- Inflatable tug-of-war setups typically measure around 20 to 30 feet in length and 10 to 15 feet in width, providing ample space for teams to compete and move around safely. The exact size can vary based on the rental company or manufacturer.</span>
							
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-37" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/37_Product_Gaint Swing/compressed_1726651559658.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/37_Product_Gaint Swing/compressed_1726651566294.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>GIANT SEWING  </h3>
                            <span class="text">Giant sewing involves using oversized needles and thread to create large-scale textile projects. Often seen in art installations or massive quilts, it combines traditional sewing techniques with dramatic dimensions, emphasizing both craftsmanship and visual impact.
</span>
                           
                            <span class="text">SIZE:- Giant sewing typically uses materials and tools on a larger scale than conventional sewing, with needles and threads often several times larger than standard sizes. Projects can range from several feet to tens of feet in dimensions, emphasizing their scale and impact.</span>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project_modal">
    <div id="blog-model-38" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/38_Product_Parasailing/compressed_1726651556781.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/38_Product_Parasailing/compressed_1726651566431.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
                        <div class="project_content">
                            <h3>PARACHUTE</h3>
                            <span class="text">Experience the thrill of skydiving with our top-quality parachutes. Engineered for safety and performance, our parachutes ensure a smooth, controlled descent. Perfect for both beginners and experienced jumpers, offering unmatched reliability and ease of use."
</span>
                           
                            <span class="text">SIZE:- The size of a parachute varies based on its type and intended use. For recreational skydiving, parachutes typically range from 20 to 30 feet in diameter. For military or professional use, they can be larger, up to 60 feet or more. The size affects the descent speed and stability.

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
    <div id="blog-model-39" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/39_Product_Bungee Tarampoline/compressed_1726651557149.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/39_Product_Bungee Tarampoline/compressed_1726651559348.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>BUNGEE TRAMPOLINE</h3>
                            <span class="text">A bungee trampoline combines trampolining with bungee jumping, allowing participants to leap higher and perform flips while securely harnessed. This exciting activity is perfect for all ages, offering a thrilling yet safe experience in adventure parks and events.

</span>
                           
                            <span class="text">SIZE:- Bungee trampolines typically require a space of around 20 to 25 feet in height and about 20 feet by 20 feet on the ground for a single unit. Multi-unit setups may need larger areas, depending on the number of trampolines.
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
    <div id="blog-model-40" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" ><i class="fa-solid fa-x"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="blog-box-item mb-0">
                        <div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/40_Product_Tree House/compressed_1726651556719.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						<div class="blog-img">
                            <figure class="mb-0">
                                <img src="assets/images/Products/40_Product_Tree House/compressed_1726651556953.jpeg" alt="blog-img" class="img-fluid">
                            </figure>
                        </div>
						
                        <div class="project_content">
                            <h3>TREEHOUSE </h3>
                            <span class="text">Experience the magic of our handcrafted treehouse at Aura Adventure Junction LLP. Nestled among lush greenery, it offers a serene escape with panoramic views, perfect for relaxation or adventure. Enjoy nature at its finest in a cosy, elevated retreat.



</span>
                           
                            <span class="text">SIZE:- Our treehouse spans a spacious 300 square feet, offering ample room for relaxation and adventure. Elevated at 15 feet above ground, it includes a comfortable living area, a cosy bedroom, and a balcony with breathtaking views, all amidst nature's canopy.
</span>
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
							<form id="enquiryForm" method="post" action="products-4.php">
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
