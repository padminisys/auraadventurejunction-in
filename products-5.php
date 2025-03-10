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
<html lang="en">

<head>
	<title>AURA ADVENTURE JUNCTION LLP</title>
	<!-- /SEO Ultimate -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
	<meta charset="utf-8">
	<!-- Favicon and Apple Icons -->
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
	<!-- reCAPTCHA Script -->
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
		<!-- Display Success or Error Messages -->
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
										<img src="assets/images/Products/41_Product_Path Bridge/compressed_1726651556637.jpeg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-41">
										<figure class="activity-plus mb-0">
											<img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
										</figure>
									</a>
								</div>

							</div>
							<div class="col-lg-12 col-md-6 col-sm-6 col-12">

								<div class="activity_wrapper position-relative">
									<figure class="activity-image">
										<img src="assets/images/Products/42_Product_Funy ponny/compressed_1726651555578.jpeg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-42">
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
								<img src="assets/images/Products/43_Product_Inflatable Hoopla/compressed_1726651555936.jpeg" alt="image" class="img-fluid">
							</figure>
							<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-43">
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
										<img src="assets/images/Products/44_Product_Human gyroscope/compressed_1726651558541.jpeg" alt="blog-img" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-44">
										<figure class="activity-plus mb-0">
											<img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
										</figure>
									</a>
								</div>


							</div>
							<div class="col-lg-12 col-md-6 col-sm-6 col-12">
								<div class="activity_wrapper position-relative">
									<figure class="activity-image mb-0">
										<img src="assets/images/Products/45_Product_Peramotor/compressed_1726651555445.jpeg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-45">
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
										<img src="assets/images/Products/46_Product_Archary game/compressed_1726651556285.jpeg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-46">
										<figure class="activity-plus mb-0">
											<img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
										</figure>
									</a>
								</div>

							</div>
							<div class="col-lg-12 col-md-6 col-sm-6 col-12">
								<div class="activity_wrapper position-relative">
									<figure class="activity-image mb-0">
										<img src="assets/images/Products/47_Product_Flying Fox/compressed_1726651556860.jpeg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-47">
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
								<img src="assets/images/Products/48_Product_Inflatable Adventure park/compressed_1726651555741.jpeg" alt="image" class="img-fluid">
							</figure>
							<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-48">
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
										<img src="assets/images/Products/49_Product_Hanging Bridge/compressed_1726651556563.jpeg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-49">
										<figure class="activity-plus mb-0">
											<img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
										</figure>
									</a>
								</div>
							</div>
							<div class="col-lg-12 col-md-6 col-sm-6 col-12">
								<div class="activity_wrapper position-relative">
									<figure class="activity-image">
										<img src="assets/images/Products/50_Product_Multi Activity Tower/Picsart_24-08-26_10-26-54-307.jpg" alt="image" class="img-fluid">
									</figure>
									<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-50">
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
							<figure class="activity-image">
								<img src="assets/images/Products/51_Product_All Type Of Bridges/compressed_1726651557518.jpeg" alt="image" class="img-fluid">
							</figure>
							<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-51">
								<figure class="activity-plus mb-0">
									<img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
								</figure>
							</a>
						</div>
					</div>

					<div class="col-lg-6 col-12">

						<div class="activity_wrapper position-relative">
							<figure class="activity-image">
								<img src="assets/images/Products/52_Product_Tyre Run And Nets/compressed_1726651562429.jpeg" alt="image" class="img-fluid">
							</figure>
							<a href="#" class="text-decoration-none" data-toggle="modal" data-target="#blog-model-52">
								<figure class="activity-plus mb-0">
									<img src="assets/images/activity3-plusicon.png" alt="image" class="img-fluid">
								</figure>
							</a>
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
						<li><a href="products-4.php">«</a></li>
						<li><a href="products.php">1</a></li>
						<li><a href="products-2.php">2</a></li>
						<li><a href="products-3.php">3</a></li>
						<li><a href="products-4.php">4</a></li>
						<li><a href="#" class="active">5</a></li>

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
										<figure class="mb-0"><img src="assets/images/footer.png" alt="image"></figure>
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
		<div id="blog-model-41" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/41_Product_Path Bridge/compressed_1726651556637.jpeg" alt="blog-img" class="img-fluid">

								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/41_Product_Path Bridge/compressed_1726651563330.jpeg" alt="blog-img" class="img-fluid">

								</figure>
							</div>

							<div class="project_content">
								<h3>PATH BRIDGE</h3>
								<span class="text">A path bridge is a narrow, elevated structure designed to span obstacles like rivers or valleys, providing a safe passage for pedestrians or cyclists. Often constructed from wood, steel, or concrete, path bridges blend functionality with nature, offering scenic views and accessibility.
								</span>

								<span class="text">SIZE:- The size of a path bridge can vary widely depending on its purpose and location. For pedestrian or cycling paths, common widths range from 1.5 to 3 meters (5 to 10 feet), allowing for safe passage. Lengths can span anywhere from a few meters to over 100 meters (328 feet) to cross rivers, gorges, or other obstacles. The height of a path bridge above ground can also vary, typically ranging from 1 to 50 meters (3 to 164 feet), depending on the terrain and the structure’s intended use.</span>
								<a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="project_modal">
		<div id="blog-model-42" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/42_Product_Funy ponny/compressed_1726651555578.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>FUNNY PONY </h3>
								<span class="text">Inflatable Funny Pony offers a whimsical, safe adventure with our inflatable pony rides. Perfect for kids' parties and events, our colourful ponies provide hours of fun and laughter. Supervised by our trained staff, every ride is both thrilling and secure.
								</span>

								<span class="text">SIZE:- Inflatable Funny Pony provides whimsical, safe inflatable pony rides for kids' parties and events. Our colourful, bouncy ponies deliver endless fun and excitement. Supervised by trained staff, every ride ensures a secure and enjoyable experience for children of all ages.

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
		<div id="blog-model-43" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/43_Product_Inflatable Hoopla/compressed_1726651555936.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/43_Product_Inflatable Hoopla/compressed_1726651556510.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="project_content">
								<h3>INFLATABLE HOOPLA</h3>
								<span class="text">Inflatable hoopla is a thrilling game featuring giant, colourful hoops that players jump through, toss, or manoeuvre around. Ideal for parties and events, it combines physical activity with fun, encouraging competition and teamwork in a lively, safe environment.
								</span>

								<span class="text">SIZE:- Inflatable hoopla games vary in size, but typically they range from 6 to 10 feet in diameter. The exact size depends on the specific design and intended use, whether for large events or smaller gatherings.
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
		<div id="blog-model-44" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/44_Product_Human gyroscope/compressed_1726651558541.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="project_content">
								<h3>HUMAN GYROSCOPE</h3>
								<span class="text">HUMAN GYROSCOPE Experience the thrill of the Human Gyroscope! Spin in all directions and feel the rush as you rotate 360 degrees. Perfect for thrill-seekers, this ride offers an exhilarating, gravity-defying adventure that promises fun and excitement for all ages.</span>

								<span class="text">SIZE:- The Human Gyroscope is designed to accommodate a variety of sizes, typically fitting individuals ranging from 4 to 6 feet tall and up to 250 pounds. For specific dimensions, checking with the equipment provider for exact specifications is recommended.</span>
								<a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="project_modal">
		<div id="blog-model-45" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/45_Product_Peramotor/compressed_1726651555445.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/45_Product_Peramotor/compressed_1726651555836.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>PERAMOTER </h3>
								<span class="text">Paragliding offers an exhilarating experience where you are connected to a motorized parachute, allowing you to soar high above the ground. This adventurous sport provides a sense of freedom in the air and an opportunity to enjoy unique and breathtaking views.</span>

								<span class="text">SIZE:- Peramoter equipment generally includes a wing that ranges from 20 to 30 feet in width and 8 to 12 feet in height. The size and shape of the wing affect its performance and stability. The height you can reach while paragliding varies depending on factors like weather conditions and equipment, but you can typically ascend to heights of 1,000 to 3,000 feet above the ground.</span>
								<a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="project_modal">
		<div id="blog-model-46" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/46_Product_Archary game/compressed_1726651556285.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/46_Product_Archary game/compressed_1726651558211.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">

								<h3>ARCHAERY GAME </h3>

								<span class="text">Experience the thrill of archery with our adventure game, where precision and focus are your keys to success. Draw your bow, aim for the target, and release. Compete for high scores in a captivating outdoor setting that challenges your skills and concentration.
									•SIZE:- In archery, bow sizes vary based on type and user preferences:

									- **Recurve Bows**: Typically range from 60 to 70 inches in length.
									- **Compound Bows**: Usually 30 to 40 inches in length.
									- **Longbows**: Generally 66 to 72 inches in length.

									Target sizes can also vary:

									- **Standard 3D Targets**: About 16 to 30 inches in diameter.
									- **Field Archery Targets**: 18 inches for indoor or 36 inches for outdoor.
									- **Target Faces**: Ranging from 40 to 122 cm in diameter, depending on competition rules.

									These sizes help cater to different skill levels and types of archery.
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
		<div id="blog-model-47" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/47_Product_Flying Fox/compressed_1726651556860.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/47_Product_Flying Fox/compressed_1726651559227.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="project_content">
								<h3>FLYING FOX</h3>
								<span class="text">The flying fox, also known as a zipline, offers an exhilarating experience as you soar through the air on a suspended cable. Perfect for adventure enthusiasts, it combines speed and height, providing a thrilling ride with breathtaking views.
								</span>

								<span class="text">SIZE:- Flying foxes, or ziplines, vary in size. Shorter versions might span 100 to 200 meters, while larger setups can exceed 1,000 meters. The height and length often depend on the terrain and desired thrill level, with some reaching impressive altitudes and distances.
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
		<div id="blog-model-48" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/48_Product_Inflatable Adventure park/compressed_1726651555741.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>INFLATABLE ADVENTURE PARK</h3>
								<span class="text">An inflatable adventure park offers a range of exciting, large-scale inflatable activities like obstacle courses, bounce houses, and slides. It provides a thrilling, family-friendly environment for all ages to enjoy active play and fun-filled experiences in a safe setting.
									•SIZE:- Here’s a general guide to the sizes of common inflatable items in an adventure park:

									1. **Inflatable Obstacle Courses**: Typically 30 to 60 feet long, 10 to 15 feet wide, and 15 to 25 feet high.
									2. **Bounce Houses**: Usually 15 to 20 feet on each side and 10 to 15 feet high.
									3. **Inflatable Slides**: Range from 15 to 30 feet in height, with widths between 10 to 20 feet.
									4. **Inflatable Water Slides**: Similar to regular slides but often a bit larger, ranging from 20 to 40 feet high.
									5. **Inflatable Ball Pits**: Typically around 10 to 15 feet in diameter and 5 to 10 feet high.
									6. **Inflatable Zorbing Balls**: Usually 8 to 10 feet in diameter.
									7. **Inflatable Bungee Trampolines**: Often 20 to 30 feet in diameter, with a height reaching up to 20 feet.
									8. **Inflatable Mazes**: Can be 30 to 50 feet long and wide, with varying heights.

									Sizes can vary based on the manufacturer and specific design.

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
		<div id="blog-model-49" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/49_Product_Hanging Bridge/compressed_1726651556563.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/49_Product_Hanging Bridge/compressed_1726651557518.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>HANGING BRIDGE</h3>
								<span class="text">A hanging bridge, suspended by cables or ropes, spans across a gap or chasm. Designed for pedestrians or vehicles, it offers a thrilling experience as it sways and moves. Such bridges blend functionality with dramatic, scenic beauty.
								</span>

								<span class="text">SIZE:- Hanging bridges vary widely in size. Small pedestrian bridges can be just a few meters long, while large suspension bridges span over several kilometres. The length, width, and height depend on their intended use and the gap they need to cross.

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
		<div id="blog-model-50" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/50_Product_Multi Activity Tower/Picsart_24-08-26_10-26-54-307.jpg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/50_Product_Multi Activity Tower/Picsart_24-08-26_10-22-14-126.jpg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>MULTI ACTIVITY TOWER </h3>
								<span class="text">A multi-activity tower offers various adventure challenges in one structure, featuring climbing walls, rope courses, zip lines, and obstacle elements. It's designed to provide diverse, thrilling experiences for different skill levels and age groups, ensuring endless fun and excitement.


								</span>

								<span class="text">SIZE:-The size of a multi-activity tower varies, but it typically ranges from 20 to 50 feet in height and can be 10 to 30 feet in width and depth. This ensures ample space for various activities while maintaining a manageable footprint.

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
		<div id="blog-model-51" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/51_Product_All Type Of Bridges/compressed_1726651563330.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/51_Product_All Type Of Bridges/compressed_1726651557518.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>ALL TYPE OF BRIDGES </h3>
								<span class="text">Adventure bridges come in various types: Suspension bridges sway with cables, zipline bridges combine gliding elements, swing bridges move as you cross, sky bridges offer high views, monkey bridges test balance, treetop bridges are elevated among trees, cables bridges use cables and planks, and suspended plank bridges create a shaky crossing experience.
								</span>

								<span class="text">SIZE:-Adventure bridges vary in size based on their type and purpose. Suspension bridges and sky bridges can span hundreds of meters, while treetop and zipline bridges typically cover shorter distances. Monkey and swing bridges are often shorter and more compact, focusing on balance and agility rather than distance.



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
		<div id="blog-model-52" class="modal fade blog-model-con" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-x"></i></span></button>
					</div>
					<div class="modal-body">
						<div class="blog-box-item mb-0">
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/52_Product_Tyre Run And Nets/compressed_1726651562063.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>
							<div class="blog-img">
								<figure class="mb-0">
									<img src="assets/images/Products/52_Product_Tyre Run And Nets/compressed_1726651562429.jpeg" alt="blog-img" class="img-fluid">
								</figure>
							</div>

							<div class="project_content">
								<h3>TYRE RUN </h3>
								<span class="text">participants running while pushing or dragging a tyre, challenging their strength and endurance. It's often used in fitness training or competitive events to build stamina, improve coordination, and enhance overall physical conditioning.
								</span>

								<span class="text">SIZE:-A standard tyre used for a tyre run typically ranges from 24 to 28 inches in diameter. The size can vary based on the activity’s difficulty level and the participants’ strength and fitness goals.

								</span>
								<a href="#" data-toggle="modal" data-target="#myModal" class="text-decoration-none all_button">Enquiry Now<i class="fa-solid fa-arrow-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Enquiry Form Modal -->
	<div class="modal-body">
		<div class="modal-box">
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content clearfix">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						<div class="modal-body" style="border: 4px solid #11bef1; border-radius: 15px;">
							<h3 class="title">Enquiry Form</h3>
							<form id="enquiryForm" method="post" action="products-5.php">
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

	<!-- Rest of the products-5.php content -->
	<!-- ... -->
	</div>
	</section>

	<!-- Footer -->
	<section class="footer-con position-relative">
		<figure class="mb-0 footer-image">
			<img src="assets/images/footer.png" alt="image" class="img-fluid">
		</figure>
		<div class="container position-relative">
			<!-- Your footer content -->
			<!-- ... -->
		</div>

		<div class="copyright">
			<p class="mb-0 text-size-14">Copyright 2024, Aura Adventure LLP All Rights Reserved.</p>
		</div>
	</section>

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

</html>