<?php
session_start();

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include PHPMailer library (make sure to install it via Composer or include the files)
// require 'path/to/PHPMailerAutoload.php'; // Uncomment this line if you're not using Composer
// Include Composer's autoloader
require 'vendor/autoload.php'; // Adjusted to include Composer's autoloader
// Initialize variables
$success = '';
$error = '';

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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Validate CSRF Token
	if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
		// Tokens do not match
		$error = 'Invalid CSRF token';
	} else {
		// Verify reCAPTCHA
		$recaptchaSecret = getenv('recaptchaSecret');
		$response = $_POST['g-recaptcha-response'];
		$remoteIp = $_SERVER['REMOTE_ADDR'];

		$recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptchaResponse = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $response . '&remoteip=' . $remoteIp);
		$recaptchaData = json_decode($recaptchaResponse);

		if (!$recaptchaData->success) {
			$error = 'reCAPTCHA verification failed. Please try again.';
		} else {
			// Sanitize and validate input
			$name  = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
			$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
			$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
			$msg   = filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_STRING);

			// Check for empty fields
			if (empty($name) || empty($phone) || empty($email) || empty($msg)) {
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
				$mail->Subject = "Contact Form Submission from " . $name;
				$mail->Body    = "Name: $name\nPhone: $phone\nEmail: $email\nMessage:\n$msg\n";

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
	<title>Contact Us | AURA ADVENTURE JUNCTION LLP</title>
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
	<!-- CSS Files -->
	<link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/js/bootstrap.min.js">
	<link rel="stylesheet" href="../../cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
		<!-- Sub banner -->

	</div>
	<!-- Contact Info -->
	<section class="contactinfo-con">
		<div class="container">
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

			<div class="row">
				<div class="col-12">
					<div class="contactinfo_content text-center" data-aos="fade-up">
						<h6>Reach Us</h6>
						<h2>Contact Information</h2>
					</div>
				</div>
			</div>
			<div class="row" data-aos="fade-up">
				<div class="col-lg-4 col-md-4 col-sm-12 col-12">
					<div class="contact-box">
						<figure class="icon">
							<img src="assets/images/contact-icon1.png" alt="image" class="img-fluid">
						</figure>
						<h4>Our Location</h4>
						<p class="text-size-18">AURA ADVENTURE JUNCTION LLP 252, GOPAL JI COLONY, GALI NO.1,SAMALKA, NEW DELHI: 110037</p>
						<div class="clearfix"></div>
						<a href="https://www.google.com/maps/place/121+King+St,+Melbourne+VIC+3000,+Australia/@-37.8172467,144.9532001,17z/data=!3m1!4b1!4m6!3m5!1s0x6ad65d4dd5a05d97:0x3e64f855a564844d!8m2!3d-37.817251!4d144.955775!16s%2Fg%2F11g0g8c54h?entry=ttu"
							class="text-decoration-none button">Get Directions<i class="fa-solid fa-arrow-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-12">
					<div class="contact-box">
						<figure class="icon">
							<img src="assets/images/contact-icon2.png" alt="image" class="img-fluid">
						</figure>
						<h4>Phone Number</h4>
						<a href="tel:9509924199" class="mb-0 text-decoration-none text-size-18">9509924199</a>
						<a href="tel:9899753892" class="text-decoration-none text-size-18">9899753892</a>
						<div class="clearfix"></div>
						<a href="tel:9899753892" class="text-decoration-none button">Call Now<i class="fa-solid fa-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-12">
					<div class="contact-box mb-0">
						<figure class="icon">
							<img src="assets/images/contact-icon3.png" alt="image" class="img-fluid">
						</figure>
						<h4>Our Email</h4>
						<a href="mailto:auraadventure82@gmail.com" class="mb-0 text-decoration-none text-size-18">auraadventure82@gmail.com</a>
						<div class="clearfix"></div>
						<a href="mailto:auraadventure82@gmail.com" class="text-decoration-none button">Email Now<i class="fa-solid fa-arrow-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Contact Form -->
	<section class="contactform-con">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="contactform_content text-center" data-aos="fade-up">
						<h6>Get in Touch</h6>
						<h2>Send us a Message</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="contact_form" data-aos="fade-up">
						<form id="contactpage" method="post" action="contact.php">
							<!-- CSRF Token -->
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-12 col-12">
									<div class="form-group">
										<input type="text" class="form_style" placeholder="Name" name="fname" id="fname">
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12 col-12">
									<div class="form-group">
										<input type="tel" class="form_style" placeholder="Phone" name="phone" id="phone">
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12 col-12">
									<div class="form-group">
										<input type="email" class="form_style" placeholder="Email" name="email" id="email">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group message">
										<textarea class="form_style" placeholder="Message" rows="3" name="msg"></textarea>
									</div>
								</div>
								<!-- Google reCAPTCHA widget -->
								<div class="col-12">
									<div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($dataSiteKey); ?>"></div> <!-- Replace with your Site Key -->
								</div>
							</div>
							<div class="text-center">
								<button id="submit" type="submit" class="submit_now">Send Now<i class="fa-solid fa-arrow-right"></i></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
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
	<script src="assets/js/contact-validate.js"></script>
	<script src="assets/js/counter.js"></script>
	<script src="assets/js/search.js"></script>
	<script src="assets/js/popup-image.js"></script>
</body>

</html>