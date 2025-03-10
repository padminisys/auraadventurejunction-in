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
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"
    />
    <meta charset="utf-8" />
    <link
      rel="apple-touch-icon"
      sizes="57x57"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="60x60"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="72x72"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="76x76"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="114x114"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="120x120"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="144x144"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="152x152"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="192x192"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="96x96"
      href="assets/images/favicon/logo.jpg"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="assets/images/favicon/logo.jpg"
    />
    <link rel="manifest" href="assets/images/favicon/manifest.json" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="msapplication-TileImage" content="../ms-icon-144x144.html" />
    <meta name="theme-color" content="#ffffff" />
    <!-- Latest compiled and minified CSS -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/js/bootstrap.min.js" />
    <!-- Font Awesome link -->
    <link
      rel="stylesheet"
      href="../../cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <!-- StyleSheet link CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link
      href="assets/css/owl.carousel.min.css"
      rel="stylesheet"
      type="text/css"
    />
    <link
      href="assets/css/owl.theme.default.min.css"
      rel="stylesheet"
      type="text/css"
    />
    <link href="assets/css/aos.css" rel="stylesheet" />
    <link
      href="assets/css/magnific-popup.css"
      rel="stylesheet"
      type="text/css"
    />

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
  </head>
  <body>
    <a
      target="_blank"
      href="https://api.whatsapp.com/send?phone=919509924199&text=aura_adventure"
      class="whatsapp-button"
      ><i class="fab fa-whatsapp"></i
    ></a>
    <!-- Back to top button -->
    <a id="button"></a>
    <div class="sub_banner position-relative">
      <header class="header">
        <div class="container">
          <nav class="navbar navbar-expand-lg navbar-light p-0">
            <a class="navbar-brand" href="index.html">
              <figure class="logo mb-0">
                <img
                  src="assets/images/logo.jpg"
                  alt="image"
                  class="img-fluid"
                />
              </figure>
            </a>
            <button
              class="navbar-toggler collapsed"
              type="button"
              data-toggle="collapse"
              data-target="#navbarSupportedContent"
              aria-controls="navbarSupportedContent"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
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
            <a class="search-box search" href="#search"
              ><i class="fa-solid fa-magnifying-glass"></i
            ></a>
            <a href="tel:+568925896325" class="text-decoration-none">
              <div class="phone-number">
                <figure class="banner-phoneicon mb-0">
                  <img
                    src="assets/images/banner-phoneicon.png"
                    alt="image"
                    class="img-fluid"
                  />
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
          <input value="" name="q" type="search" placeholder="Type to Search" />
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

    <br />
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
                    <figure class="activity-image">
                      <img
                        src="assets/images/Products/1_Product_Trampoline/compressed_1726651565997.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-1"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
                      </figure>
                    </a>
                  </div>
                </div>
                <div class="col-lg-12 col-md-6 col-sm-6 col-12" id="waterrolar">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image mb-0">
                      <img
                        src="assets/images/Products/2_Product_Inflatable Water roller/compressed_1726651563215.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-2"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
                      </figure>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-12" id="bodyZorbing">
              <div class="activity_wrapper position-relative">
                <figure class="activity-image mb-0">
                  <img
                    src="assets/images/Products/3_Product_Inflatbale Land Zorbing Ball/compressed_1726651562991.jpeg"
                    alt="image"
                    class="img-fluid"
                  />
                </figure>
                <a
                  href="#"
                  class="text-decoration-none"
                  data-toggle="modal"
                  data-target="#blog-model-3"
                >
                  <figure class="activity-plus mb-0">
                    <img
                      src="assets/images/activity3-plusicon.png"
                      alt="image"
                      class="img-fluid"
                    />
                  </figure>
                </a>
              </div>
            </div>
            <div class="col-lg-3 col-12">
              <div class="row">
                <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image">
                      <img
                        src="assets/images/Products/4_Product_Inflatable Gladiator Duel/compressed_1726651564887.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-4"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
                      </figure>
                    </a>
                  </div>
                </div>
                <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image mb-0">
                      <img
                        src="assets/images/Products/5_Product_Inflatable Micky Mouse/compressed_1726651565081.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-5"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
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
                <div class="col-lg-12 col-md-6 col-sm-6 col-12" id="Zorbing">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image mb-0">
                      <img
                        src="assets/images/Products/8_Product_Inflatable Body Zorbing Ball/compressed_1726651561532.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-8"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
                      </figure>
                    </a>
                  </div>
                </div>
                <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image mb-0">
                      <img
                        src="assets/images/Products/7_Product_ Inflatable Balling Alley/compressed_1726651559579.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-7"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
                      </figure>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-12">
              <div class="activity_wrapper position-relative">
                <figure class="activity-image mb-0">
                  <img
                    src="assets/images/Products/10_Product_Bungee Injection/compressed_1726651568202.jpeg"
                    alt="image"
                    class="img-fluid"
                  />
                </figure>
                <a
                  href="#"
                  class="text-decoration-none"
                  data-toggle="modal"
                  data-target="#blog-model-10"
                >
                  <figure class="activity-plus mb-0">
                    <img
                      src="assets/images/activity3-plusicon.png"
                      alt="image"
                      class="img-fluid"
                    />
                  </figure>
                </a>
              </div>
            </div>
            <div class="col-lg-3 col-12">
              <div class="row">
                <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image">
                      <img
                        src="assets/images/Products/9_Product_Human Sling Shoot/compressed_1726651560135.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-9"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
                      </figure>
                    </a>
                  </div>
                </div>
                <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                  <div class="activity_wrapper position-relative">
                    <figure class="activity-image">
                      <img
                        src="assets/images/Products/6_Product_Hot Air Balloon/compressed_1726651561945.jpeg"
                        alt="image"
                        class="img-fluid"
                      />
                    </figure>
                    <a
                      href="#"
                      class="text-decoration-none"
                      data-toggle="modal"
                      data-target="#blog-model-6"
                    >
                      <figure class="activity-plus mb-0">
                        <img
                          src="assets/images/activity3-plusicon.png"
                          alt="image"
                          class="img-fluid"
                        />
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
              <li><a class="" href="#">«</a></li>
              <li><a href="#" class="active">1</a></li>
              <li><a href="products-2.php">2</a></li>
              <li><a href="products-3.php">3</a></li>
              <li><a href="products-4.php">4</a></li>
              <li><a href="products-5.php">5</a></li>

              <li><a href="products-2.php">»</a></li>
            </ul>
          </div>
        </div>
        <!--wrapper-->
      </div>
    </section>
    <!-- Testimonial -->

    <!-- Footer -->
    <!-- Footer -->
    <section class="footer-con position-relative">
      <figure class="mb-0 footer-image">
        <img src="assets/images/footer.png" alt="image" class="img-fluid" />
      </figure>
      <div class="container position-relative">
        <div class="middle_portion">
          <div class="row">
            <div class="col-xl-10 col-12 mx-auto">
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                  <div class="logo-content">
                    <h6 class="heading">About us</h6>

                    <p class="text text-size-16 mb-0">
                      We are pleased to introduce our self as one of the most
                      prominent names engaged in manufacturing & supplying of
                      high-quality Inflatables like Advertising Sky Balloon,
                      Inflatable Bouncer, Printed Balloon, Inflatable Zorbing
                      Balls, Inflatable Arches, Advertising Back Pack Balloon,
                      and Character Inflatables since 2006.
                    </p>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                  <div class="links">
                    <h6 class="heading">Useful Links</h6>
                    <ul class="list-unstyled mb-0">
                      <li>
                        <i class="fa-solid fa-circle"></i
                        ><a href="#" class="text-decoration-none">Home</a>
                      </li>
                      <li>
                        <i class="fa-solid fa-circle"></i
                        ><a href="#" class="text-decoration-none">About</a>
                      </li>
                      <li>
                        <i class="fa-solid fa-circle"></i
                        ><a href="#" class="text-decoration-none">Services</a>
                      </li>
                      <li>
                        <i class="fa-solid fa-circle"></i
                        ><a href="#" class="text-decoration-none">Blog</a>
                      </li>
                      <li>
                        <i class="fa-solid fa-circle"></i
                        ><a href="#" class="text-decoration-none">Contact Us</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                  <div class="contact">
                    <h6 class="heading">Contact Info</h6>
                    <ul class="list-unstyled mb-0">
                      <li class="text">
                        <i class="fa-solid fa-phone"></i>
                        <a
                          href="tel:+91-9899753892"
                          class="text-decoration-none"
                          >+91-9899753892, +91-9509924199</a
                        >
                      </li>
                      <li class="text">
                        <i class="fa-solid fa-envelope"></i>
                        <a
                          href="mailto:info@auraadventure.in"
                          class="text-decoration-none"
                          >info@auraadventure.in</a
                        >
                        <a
                          href="mailto:auraadventure82@gmail.com"
                          class="text-decoration-none"
                          >auraadventure82@gmail.com</a
                        >
                      </li>
                      <li class="text">
                        <i class="fa-solid fa-location-dot"></i>
                        <p class="address mb-0">
                          AURA ADVENTURE JUNCTION LLP 252, GOPAL JI COLONY, GALI
                          NO.1,SAMALKA, NEW DELHI : 110037
                        </p>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                  <div class="icon">
                    <h6 class="heading">Social Networks</h6>
                    <ul class="list-unstyled mb-0 social-icons">
                      <li>
                        <a
                          href="https://www.facebook.com/profile.php?id=61564764900354&mibextid=ZbWKwL"
                          class="text-decoration-none"
                          ><i
                            class="fa-brands fa-facebook-f social-networks"
                            aria-hidden="true"
                          ></i
                        ></a>
                      </li>
                      <li>
                        <a
                          href="https://x.com/aura_adventure?t=vnor0jHpLWL81xzB7ult-g&s=09"
                          class="text-decoration-none"
                          ><i
                            class="fa-brands fa-x-twitter social-networks"
                            aria-hidden="true"
                          ></i
                        ></a>
                      </li>
                      <li>
                        <a
                          href="https://youtube.com/@auraadventurejunction_in?si=I97Q9Y2tihNwPJxS"
                          class="text-decoration-none"
                          ><i
                            class="fa-brands fa-youtube social-networks"
                            aria-hidden="true"
                          ></i
                        ></a>
                      </li>
                      <li>
                        <a
                          href="https://www.instagram.com/auraadventurejunctionllp?igsh=cnlmNnlzOWc4eTN4"
                          class="text-decoration-none"
                          ><i
                            class="fa-brands fa-instagram social-networks"
                            aria-hidden="true"
                          ></i
                        ></a>
                      </li>
                    </ul>
                    <br />
                    <a
                      href="index.html"
                      class="footer-logo"
                      style="align-items: center"
                    >
                      <figure class="mb-0">
                        <img src="assets/images/footer.png" alt="image" />
                      </figure>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="copyright">
        <p class="mb-0 text-size-14">
          Copyright 2024, Aura Adventure LLP All Rights Reserved.
        </p>
      </div>
    </section>
    <!-- Project SECTION POPUP -->
    <div class="project_modal">
      <div
        id="blog-model-1"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/1_Product_Trampoline/compressed_1726651565997.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/1_Product_Trampoline/compressed_1726651564523.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>TRAMPOLINE</h3>
                  <span class="text"
                    >A trampoline is a spring-based or elastic device used for
                    jumping and acrobatic exercises. It consists of a strong,
                    taut fabric stretched over a frame, allowing users to bounce
                    into the air for fun, fitness, or gymnastics training.</span
                  >

                  <span class="text"
                    >SIZE:- Our trampolines, available in 10, 12, 14, and 16
                    feet sizes, are perfect for kids and adults. Built with
                    durable frames and secure enclosures, they provide a safe,
                    fun, and exciting way to enjoy outdoor play and exercise for
                    all ages.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="project_modal">
      <div
        id="blog-model-2"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/2_Product_Inflatable Water roller/compressed_1726651564763.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/2_Product_Inflatable Water roller/compressed_1726651563215.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>WATER ROLLER</h3>
                  <span class="text"
                    >An inflatable water roller is a large, cylindrical,
                    transparent tube made of durable PVC material. Users climb
                    inside and walk or roll across the water's surface,
                    providing a fun, safe, and buoyant experience in pools,
                    lakes, or water parks.</span
                  >

                  <span class="text"
                    >SIZE:- Inflatable water rollers typically range in size
                    from 2 to 3 meters (6.5 to 10 feet) in diameter and about
                    2.5 to 3 meters (8 to 10 feet) in length. The size can vary
                    depending on the manufacturer and the intended user
                    capacity.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="project_modal">
      <div
        id="blog-model-3"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/3_Product_Inflatbale Land Zorbing Ball/compressed_1726651565836.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/3_Product_Inflatbale Land Zorbing Ball/compressed_1726651562991.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>INFLATABLE LAND ZORBING BALL</h3>
                  <span class="text"
                    >A land zorbing ball is a large, transparent inflatable
                    sphere made of durable plastic. It allows a person to roll
                    inside, cushioned by an air layer, offering a thrilling
                    experience as they tumble across terrain or down
                    slopes.</span
                  >

                  <span class="text"
                    >SIZE:- zorbing balls typically range in size from 2.5 to
                    3.5 meters (8 to 12 feet) in diameter. The inner sphere,
                    where the rider sits, is usually around 1.8 to 2 meters (6
                    to 7 feet) in diameter.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="project_modal">
      <div
        id="blog-model-4"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/4_Product_Inflatable Gladiator Duel/compressed_1726651561620.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/4_Product_Inflatable Gladiator Duel/compressed_1726651564887.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>GLADIATOR DUEL</h3>
                  <span class="text"
                    >An inflatable gladiator duel is a fun, safe activity where
                    two participants, equipped with padded jousting poles,
                    attempt to knock each other off raised inflatable podiums in
                    a cushioned arena. It's perfect for parties, combining
                    balance, strength, and friendly competition.</span
                  >

                  <span class="text"
                    >SIZE:- The size of an inflatable gladiator duel typically
                    ranges from 15 to 20 feet (4.5 to 6 meters) in length and 12
                    to 15 feet (3.5 to 4.5 meters) in width. The height can be
                    around 3 to 5 feet (1 to 1.5 meters), ensuring a safe,
                    cushioned fall for participants. Sizes may vary depending on
                    the manufacturer or setup.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="project_modal">
      <div
        id="blog-model-5"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/5_Product_Inflatable Micky Mouse/compressed_1726651560861.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/5_Product_Inflatable Micky Mouse/compressed_1726651565081.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>INFLATABLE MICKY MOUSE</h3>
                  <span class="text"
                    >An inflatable Mickey Mouse is a large, air-filled structure
                    resembling the iconic Disney character. Made from durable
                    materials like vinyl, it features Mickey's signature red
                    shorts, yellow shoes, and large ears, and often includes
                    internal lights for nighttime visibility.</span
                  >

                  <span class="text"
                    >SIZE:-The size of an inflatable Mickey Mouse can vary
                    widely. Smaller versions might be around 4 to 6 feet tall,
                    while larger displays can range from 10 to 20 feet or more
                    in height. For event or promotional purposes, some can be
                    even larger, depending on the specific needs and space
                    available.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="project_modal">
      <div
        id="blog-model-6"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/6_Product_Hot Air Balloon/compressed_1726651561945.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/6_Product_Hot Air Balloon/compressed_1726651565421.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>HOT AIR BALLOON</h3>
                  <span class="text"
                    >A hot air balloon consists of a large fabric envelope
                    filled with heated air and a basket for passengers. A burner
                    heats the air inside the envelope, causing the balloon to
                    rise. It provides scenic, leisurely aerial views.</span
                  >

                  <span class="text"
                    >SIZE:- 2 SIZE SUCH 105,180. Hot air balloons vary in size,
                    typically ranging from 40 to 100 feet in height and 30 to 70
                    feet in diameter when inflated. The capacity of the basket
                    can vary, accommodating 2 to 20 passengers or more.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="project_modal">
      <div
        id="blog-model-7"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/7_Product_ Inflatable Balling Alley/compressed_1726651559579.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/7_Product_ Inflatable Balling Alley/compressed_1726651558056.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>INFLATABLE BALLING ALLEY</h3>
                  <span class="text"
                    >An inflatable balling alley features a soft, cushioned
                    surface designed for safe and fun bowling. Players roll
                    oversized, lightweight balls down a vibrant, bouncy lane,
                    aiming for inflatable pins. It's perfect for all ages,
                    combining excitement with a gentle landing.</span
                  >

                  <span class="text"
                    >SIZE:- An inflatable bowling alley typically measures
                    around 20 to 30 feet in length and 10 to 15 feet in width.
                    The size can vary based on the specific design and
                    manufacturer.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="project_modal">
      <div
        id="blog-model-8"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/8_Product_Inflatable Body Zorbing Ball/compressed_1726651561532.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/8_Product_Inflatable Body Zorbing Ball/IMG-20240827-WA0041.jpg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>INFLATABLE BODY ZORB BALL</h3>
                  <span class="text"
                    >An inflatable body zorb ball is a large, transparent,
                    air-cushioned sphere that allows a person to roll and bounce
                    safely inside. It's perfect for thrilling, bouncing
                    adventures and can be used on various terrains for a unique
                    experience.</span
                  >

                  <span class="text"
                    >SIZE:- Inflatable body zorb balls typically range from 2.5
                    to 3 meters (8 to 10 feet) in diameter. This size provides
                    ample space for a person to move around comfortably while
                    ensuring safety and stability during use.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="project_modal">
      <div
        id="blog-model-9"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/9_Product_Human Sling Shoot/compressed_1726651560135.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/9_Product_Human Sling Shoot/compressed_1726651567352.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>HUMAN SLING SHOOT</h3>
                  <span class="text"
                    >A human slingshot is an exhilarating activity where
                    participants are harnessed and launched into the air using
                    elastic cords. It delivers a high-adrenaline experience and
                    requires strict safety measures. Ideal for thrill-seekers
                    and adventure enthusiasts.</span
                  >

                  <span class="text"
                    >SIZE:- A human slingshot setup typically involves a launch
                    platform and a large elastic cord. The platform is usually
                    around 10-15 feet high, while the elastic cord needs to be
                    strong and long enough to propel participants safely. The
                    exact dimensions can vary based on the specific design and
                    safety requirements.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="project_modal">
      <div
        id="blog-model-10"
        class="modal fade blog-model-con"
        tabindex="-1"
        style="display: none"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true"><i class="fa-solid fa-x"></i></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="blog-box-item mb-0">
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/10_Product_Bungee Injection/compressed_1726651568202.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="blog-img">
                  <figure class="mb-0">
                    <img
                      src="assets/images/Products/10_Product_Bungee Injection/compressed_1726651558944.jpeg"
                      alt="blog-img"
                      class="img-fluid"
                    />
                  </figure>
                </div>
                <div class="project_content">
                  <h3>BUNGEE INJECTION</h3>
                  <span class="text"
                    >Bungee injection, commonly known as bungee jumping,
                    involves leaping from a high platform while connected to a
                    large elastic cord. The cord stretches and recoils, creating
                    a thrilling experience of free fall and rebound, combining
                    adrenaline and gravity-defying fun.</span
                  >

                  <span class="text"
                    >SIZE:- The size of the bungee cord varies dependin on the
                    jump's height and the jumper's weight, but it typically
                    ranges from 8 to 12 meters (26 to 39 feet) when unstretched.
                    The cord's elasticity allows it to stretch significantly
                    during the jump.</span
                  >
                  <a
                    href="#"
                    data-toggle="modal"
                    data-target="#myModal"
                    class="text-decoration-none all_button"
                    >Enquiry Now<i class="fa-solid fa-arrow-right"></i
                  ></a>
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
							<form id="enquiryForm" method="post" action="products.php">
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
