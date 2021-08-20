<?php
include_once "./init.php";

?>
<!DOCTYPE HTML>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Mercury &mdash; School Management System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="School Management System. Equiped for all forms of management" />
	<meta name="keywords" content="school management system,SMS,LMS,Gambia,Gambia school management system,Ministry of education,education,ERP,Moodle" />
	<meta name="author" content="Caleb Chibuike Okpara" />

	<!-- Facebook and Twitter integration -->
	<meta property="og:title" content="" />
	<meta property="og:image" content="" />
	<meta property="og:url" content="" />
	<meta property="og:site_name" content="" />
	<meta property="og:description" content="" />
	<meta name="twitter:title" content="" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:url" content="" />
	<meta name="twitter:card" content="" />

	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:300,400" rel="stylesheet">

	<!-- Animate.css -->
	<link rel="stylesheet" href="css/animate.css">
	<!-- Icomoon Icon Fonts-->
	<link rel="stylesheet" href="css/icomoon.css">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="css/bootstrap.css">

	<!-- Magnific Popup -->
	<link rel="stylesheet" href="css/magnific-popup.css">

	<!-- Owl Carousel  -->
	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">

	<!-- Flexslider  -->
	<link rel="stylesheet" href="css/flexslider.css">

	<!-- Pricing -->
	<link rel="stylesheet" href="css/pricing.css">

	<!-- Theme style  -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Modernizr JS -->
	<script src="js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="js/respond.min.js"></script>
	<![endif]-->

	<style>
		html {
			scroll-behavior: smooth;
		}
	</style>

</head>

<body>

	<div class="fh5co-loader"></div>

	<div id="page">
		<nav class="fh5co-nav" role="navigation">
			<div class="top">
				<div class="container">
					<div class="row">
						<div class="col-xs-12 text-right">
							<p class="site"><?= SCHOOL_URL; ?></p>
							<p class="num">Call: +220 7024725</p>
							<!-- <ul class="fh5co-social">
								<li><a href="#"><i class="icon-facebook2"></i></a></li>
								<li><a href="#"><i class="icon-twitter2"></i></a></li>
								<li><a href="#"><i class="icon-dribbble2"></i></a></li>
								<li><a href="#"><i class="icon-github"></i></a></li>
							</ul> -->
						</div>
					</div>
				</div>
			</div>
			<div class="top-menu">
				<div class="container">
					<div class="row">
						<div class="col-xs-2">
							<div id="fh5co-logo"><a href="index.php"><i class="icon-study"></i> Mercury</a></div>
						</div>

						<div class="col-xs-10 text-right menu-1">
							<ul>
								<li class="active"><a href="index.php">Home</a></li>
								<li><a href="#features">Features</a></li>
								<!-- <li><a href="teacher.html">Teacher</a></li> -->
								<li><a href="#about-us">About</a></li>
								<li><a href="#pricing-plans">Pricing</a></li>
								<li><a href="#contact">Contact</a></li>
								<li class="btn-cta"><a href="login.php"><span>Login</span></a></li>
								<li class="btn-cta"><a href="createschool.php"><span>Create your School</span></a></li>
							</ul>
						</div>
					</div>
					<div class="col-xs-12">
						<p class="lead" style="font-weight: bold;">Sign up now and get the first 3 months for FREE</p>
					</div>
				</div>
			</div>
		</nav>

		<aside id="fh5co-hero" class="col-lg-12 col-md-11 col-sm-10 mx-auto">
			<div class="flexslider">
				<ul class="slides">
					<li style="background-image: url(images/four.jpg);">
						<div class="overlay-gradient"></div>
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2 text-center slider-text">
									<div class="slider-text-inner">
										<h1>The Great Aim of Education is not Knowledge, But Action &AMP; True Management</h1>

										<p style="color: white !important">Sign up now and get the first 3 months for FREEE!!!!</p>
										<p><a class="btn btn-primary btn-lg" href="login.php">Login</a></p>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li style="background-image: url(images/two.jpg);">
						<div class="overlay-gradient"></div>
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2 text-center slider-text">
									<div class="slider-text-inner">
										<h1>The Roots of Education are Bitter, But the Fruit is Sweet</h1>
										<p style="color: white !important">Sign up now and get the first 3 months for FREEE!!!!</p>
										<p><a class="btn btn-primary btn-lg" href="createschool.php">Start Managing Now!</a></p>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li style="background-image: url(images/three.jpg);">
						<div class="overlay-gradient"></div>
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2 text-center slider-text">
									<div class="slider-text-inner">
										<h1>The Great Aim of Education is not Knowledge, But Action &AMP; True Management</h1>

										<p style="color: white !important">Sign up now and get the first 3 months for FREEE!!!!</p>
										<p><a class="btn btn-primary btn-lg" href="login.php">Login</a></p>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li style="background-image: url(images/seven.jpg);">
						<div class="overlay-gradient"></div>
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2 text-center slider-text">
									<div class="slider-text-inner">
										<h1>The Great Aim of Education is not Knowledge, But Action &AMP; True Management</h1>

										<p style="color: white !important">Sign up now and get the first 3 months for FREEE!!!!</p>
										<p><a class="btn btn-primary btn-lg" href="login.php">Login</a></p>
									</div>
								</div>
							</div>
						</div>
					</li>
					<li style="background-image: url(images/six.jpg);">
						<div class="overlay-gradient"></div>
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2 text-center slider-text">
									<div class="slider-text-inner">
										<h1>We Help You to Manage Your School Data and Much more</h1>

										<p style="color: white !important">Sign up now and get the first 3 months for FREEE!!!!</p>
										<p><a class="btn btn-primary btn-lg" href="createschool.php">Start Managing Now!</a></p>
									</div>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</aside>

		<div id="fh5co-course-categories">
			<div class="container" id="features">
				<div class="row animate-box">
					<div class="col-md-6 col-md-offset-3 text-center fh5co-heading">
						<h2>Our Services</h2>
						<p>Below are the lists of services we provide to ensure the smooth run of your entire process.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 col-sm-6 text-center animate-box">
						<div class="services">
							<span class="icon">
								<i class="icon-shop"></i>
							</span>
							<div class="desc">
								<h3><a href="#">Data Housing</a></h3>
								<p>All your data are stored and 'backed-up' regularly and retrieved based on request.</p>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 text-center animate-box">
						<div class="services">
							<span class="icon">
								<i class="icon-heart4"></i>
							</span>
							<div class="desc">
								<h3><a href="#">Health &amp; Psychology</a></h3>
								<p>Reduced stress of data summation and analysis. Spend more time consuming than analyzing</p>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 text-center animate-box">
						<div class="services">
							<span class="icon">
								<i class="icon-banknote"></i>
							</span>
							<div class="desc">
								<h3><a href="#">Accounting</a></h3>
								<p>Built-in Tuition Fee Management. Expense Tracker and Income Tracker</p>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 text-center animate-box">
						<div class="services">
							<span class="icon">
								<i class="icon-lab2"></i>
							</span>
							<div class="desc">
								<h3><a href="#">Technology</a></h3>
								<p>In-house Notification: Send notifications to parents, staffs and students for free.</p>
							</div>
						</div>
					</div>

					<div class="col-md-3 col-sm-6 text-center animate-box">
						<div class="services">
							<span class="icon">
								<i class="icon-world"></i>
							</span>
							<div class="desc">
								<h3><a href="#">Web Academia</a></h3>
								<p>Manage all academic functionalities like: Progress report, grading sheet, attendance collection and many more...</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- <div id="fh5co-counter" class="fh5co-counters" style="background-image: url(images/img_bg_4.jpg);" data-stellar-background-ratio="0.5">
			<div class="overlay"></div>
			<div class="container">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<div class="row">
							<div class="col-md-3 col-sm-6 text-center animate-box">
								<span class="icon"><i class="icon-world"></i></span>
								<span class="fh5co-counter js-counter" data-from="0" data-to="3297" data-speed="5000" data-refresh-interval="50"></span>
								<span class="fh5co-counter-label">Foreign Followers</span>
							</div>
							<div class="col-md-3 col-sm-6 text-center animate-box">
								<span class="icon"><i class="icon-study"></i></span>
								<span class="fh5co-counter js-counter" data-from="0" data-to="3700" data-speed="5000" data-refresh-interval="50"></span>
								<span class="fh5co-counter-label">Students Enrolled</span>
							</div>
							<div class="col-md-3 col-sm-6 text-center animate-box">
								<span class="icon"><i class="icon-bulb"></i></span>
								<span class="fh5co-counter js-counter" data-from="0" data-to="5034" data-speed="5000" data-refresh-interval="50"></span>
								<span class="fh5co-counter-label">Classes Complete</span>
							</div>
							<div class="col-md-3 col-sm-6 text-center animate-box">
								<span class="icon"><i class="icon-head"></i></span>
								<span class="fh5co-counter js-counter" data-from="0" data-to="1080" data-speed="5000" data-refresh-interval="50"></span>
								<span class="fh5co-counter-label">Certified Teachers</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->


		<!-- <div id="fh5co-testimonial" style="background-image: url(images/school.jpg);">
			<div class="overlay"></div>
			<div class="container">
				<div class="row animate-box">
					<div class="col-md-6 col-md-offset-3 text-center fh5co-heading">
						<h2><span>Testimonials</span></h2>
					</div>
				</div>
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<div class="row animate-box">
							<div class="owl-carousel owl-carousel-fullwidth">
								<div class="item">
									<div class="testimony-slide active text-center">
										<div class="user" style="background-image: url(images/person1.jpg);"></div>
										<span>Mary Walker<br><small>Students</small></span>
										<blockquote>
											<p>&ldquo;Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.&rdquo;</p>
										</blockquote>
									</div>
								</div>
								<div class="item">
									<div class="testimony-slide active text-center">
										<div class="user" style="background-image: url(images/person2.jpg);"></div>
										<span>Mike Smith<br><small>Students</small></span>
										<blockquote>
											<p>&ldquo;Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.&rdquo;</p>
										</blockquote>
									</div>
								</div>
								<div class="item">
									<div class="testimony-slide active text-center">
										<div class="user" style="background-image: url(images/person3.jpg);"></div>
										<span>Rita Jones<br><small>Teacher</small></span>
										<blockquote>
											<p>&ldquo;Far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.&rdquo;</p>
										</blockquote>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->

		<div id="pricing-plans">
			<div id="fh5co-pricing" class="fh5co-bg-section">
				<div class="container">
					<div class="row animate-box">
						<div class="col-md-6 col-md-offset-3 text-center fh5co-heading">
							<h2>Plan &amp; Pricing</h2>
							<p>Below is the subscription plans.</p>
						</div>
					</div>
					<div class="row">
						<div class="pricing pricing--rabten">
							<div class="col-md-4 animate-box">
								<div class="pricing__item">
									<div class="wrap-price">
										<!-- <div class="icon icon-user2"></div> -->
										<h3 class="pricing__title">Trial</h3>
										<!-- <p class="pricing__sentence">Single user license</p> -->
									</div>
									<div class="pricing__price">
										<span class="pricing__anim pricing__anim--1">
											<span class="pricing__currency">$</span>0
										</span>
										<span class="pricing__anim pricing__anim--2">
											<span class="pricing__period">per year</span>
										</span>
									</div>
									<div class="wrap-price">
										<ul class="pricing__feature-list">
											<li class="pricing__feature">Unlimited Trial</li>
											<li class="pricing__feature">Limited Functionalities</li>
											<li class="pricing__feature">Max. 10 Students per class</li>
											<li class="pricing__feature">No Supporter</li>
											<li class="pricing__feature">Limited Response Rate</li>
											<li class="pricing__feature">No Academic Functions</li>
											<li class="pricing__feature">No Tuition Fee Management</li>
											<li class="pricing__feature">Not Secured <br>(account can be deleted)</li>
										</ul>
									</div>
								</div>
							</div>

							<div class="col-md-4 animate-box" style="margin-bottom: 10px !important;">
								<div class="pricing__item">
									<div class="wrap-price">
										<!-- <div class="icon icon-store"></div> -->
										<h3 class="pricing__title">Bronze</h3>
										<!-- <p class="pricing__sentence">Up to 5 users</p> -->
									</div>
									<div class="pricing__price">
										<span class="pricing__anim pricing__anim--1">
											<span class="pricing__currency">$</span>50.25
										</span>
										<span class="pricing__anim pricing__anim--2">
											<span class="pricing__period">per 3 months</span>
										</span>
									</div>
									<div class="wrap-price">
										<ul class="pricing__feature-list">
											<li class="pricing__feature">Three Months Standard Access</li>
											<li class="pricing__feature">Unlimited Functionalities</li>
											<li class="pricing__feature">Max. 50 Students per class</li>
											<li class="pricing__feature">Random Supporter</li>
											<li class="pricing__feature">Academic Functions</li>
											<li class="pricing__feature">Standard Updates</li>
											<li class="pricing__feature">Tuition Fee Management</li>
											<li class="pricing__feature">Unlimited Registered User</li>
											<li class="pricing__feature">SMS & Mail Notification</li>
										</ul>
									</div>
								</div>
							</div>

							<div class="col-md-4 animate-box">
								<div class="pricing__item">
									<div class="wrap-price">
										<!-- <div class="icon icon-store"></div> -->
										<h3 class="pricing__title">Silver</h3>
										<!-- <p class="pricing__sentence">Up to 5 users</p> -->
									</div>
									<div class="pricing__price">
										<span class="pricing__anim pricing__anim--1">
											<span class="pricing__currency">$</span>90.25
										</span>
										<span class="pricing__anim pricing__anim--2">
											<span class="pricing__period">per six months</span>
										</span>
									</div>
									<div class="wrap-price">
										<ul class="pricing__feature-list">
											<li class="pricing__feature">Six Months Standard Access</li>
											<li class="pricing__feature">Unlimited Functionalities</li>
											<li class="pricing__feature">Max. 50 Students per class</li>
											<li class="pricing__feature">Dedicated Supporter</li>
											<li class="pricing__feature">Unlimited Academic Functions</li>
											<li class="pricing__feature">Unlimited Updates</li>
											<li class="pricing__feature">Tuition Fee Management with Notifiable</li>
											<li class="pricing__feature">Unlimited Registered Users</li>
											<li class="pricing__feature">SMS & Mail Notication</li>
											<li class="pricing__feature">Security</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-md-4 animate-box">
								<div class="pricing__item">
									<div class="wrap-price">
										<!-- <div class="icon icon-home2"></div> -->
										<h3 class="pricing__title">Gold</h3>
										<!-- <p class="pricing__sentence">Unlimited users</p> -->
									</div>
									<div class="pricing__price">
										<span class="pricing__anim pricing__anim--1">
											<span class="pricing__currency">$</span>140.25
										</span>
										<span class="pricing__anim pricing__anim--2">
											<span class="pricing__period">per year</span>
										</span>
									</div>
									<div class="wrap-price">
										<ul class="pricing__feature-list">
											<li class="pricing__feature">One Year Standard Access</li>
											<li class="pricing__feature">Unlimited Functionalities</li>
											<li class="pricing__feature">Max. 60 Students per class</li>
											<li class="pricing__feature">Dedicated Supporter</li>
											<li class="pricing__feature">Pro-active Academic Functions</li>
											<li class="pricing__feature">Unlimied Updates</li>
											<li class="pricing__feature">Tuition Fee Management with Notifiable</li>
											<li class="pricing__feature">Unlimited Registered User</li>
											<li class="pricing__feature">SMS, WhatsApp & Mail Notification</li>
											<li class="pricing__feature">Maximum Security</li>
											<li class="pricing__feature">Data Backup</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-lg-6 col-md-9 col-sm-12 col-xs-12 mt-4" style="position: relative;top:50%;left:50%; transform: translateX(-50%)" id="contact">
			<div class="card">
				<div class="card-body">
					<h3 class="text-center">Contact Us</h3>
					<form action="" method="post">
						<div class="form-group">
							<label for="subject">Subject <span class="text-danger">*</span></label>
							<input type="text" name="subject" id="subject" placeholder="Subject" class="form-control">
						</div>
						<div class="form-group">
							<label for="subject">Email <span class="text-danger">*</span></label>
							<input type="email" name="email" id="email" placeholder="Email" class="form-control">
						</div>
						<div class="form-group">
							<label for="subject">Body <span class="text-danger">*</span></label>
							<textarea name="body" id="body" class="form-control" cols="30" rows="5" placeholder="Message Body"></textarea>
						</div>
						<div class="form-group text-center">
							<input type="submit" value="Send" class="btn btn-success" id="send">
						</div>
					</form>
				</div>
			</div>
		</div>

		<footer id="fh5co-footer" role="contentinfo" style="background-image: url(images/img_bg_4.jpg);">

			<div class="overlay"></div>
			<div class="container">
				<div class="row row-pb-md">
					<div class="col-md-3 fh5co-widget" id="about-us">
						<h3>About Us</h3>
						<p>Mercury School Management System is a data-driven solution. We take care of all the hassle of managing a school effectively.<br>Mercury - SMS is the cheapest management software in The Gambia.</p>
					</div>

					<!-- <div class="col-md-4 col-sm-4 col-xs-6 col-md-push-1 fh5co-widget">
						<h3>Engage us</h3>
						<ul class="fh5co-footer-links">
							<li><a href="#">Marketing</a></li>
							<li><a href="#">Visual Assistant</a></li>
							<li><a href="#">System Analysis</a></li>
							<li><a href="#">Advertise</a></li>
						</ul>
					</div> -->

					<div class="col-md-4 col-sm-4 col-xs-6 col-md-push-1 fh5co-widget">
						<h3>Legal</h3>
						<ul class="fh5co-footer-links">
							<!-- <li><a href="#">Find Designers</a></li>
							<li><a href="#">Find Developers</a></li> -->
							<li><a href="terms.html">Terms and Conditions</a></li>
							<!-- <li><a href="#">Advertise</a></li>
							<li><a href="#">API</a></li> -->
						</ul>
					</div>
				</div>

				<div class="row copyright">
					<div class="col-md-12 text-center">
						<p>
							<small class="block">&copy; <?= date("Y") ?> Mercury School Management System. All Rights Reserved.</small>
							<small class="block">Created with 💜 by <a href="#" target="_blank">Caleb Chibuike Okpara</a> </small>
						</p>
					</div>
				</div>

			</div>
		</footer>
	</div>

	<div class="gototop js-top">
		<a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
	</div>

	<!-- jQuery -->
	<script src="js/jquery.min.js"></script>
	<!-- jQuery Easing -->
	<script src="js/jquery.easing.1.3.js"></script>
	<!-- Bootstrap -->
	<script src="js/bootstrap.min.js"></script>
	<!-- Waypoints -->
	<script src="js/jquery.waypoints.min.js"></script>
	<!-- Stellar Parallax -->
	<script src="js/jquery.stellar.min.js"></script>
	<!-- Carousel -->
	<script src="js/owl.carousel.min.js"></script>
	<!-- Flexslider -->
	<script src="js/jquery.flexslider-min.js"></script>
	<!-- countTo -->
	<script src="js/jquery.countTo.js"></script>
	<!-- Magnific Popup -->
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/magnific-popup-options.js"></script>
	<!-- Count Down -->
	<script src="js/simplyCountdown.js"></script>
	<!-- Main -->
	<script src="js/main.js"></script>
	<script>
		var d = new Date(new Date().getTime() + 1000 * 120 * 120 * 2000);

		// default example
		simplyCountdown('.simply-countdown-one', {
			year: d.getFullYear(),
			month: d.getMonth() + 1,
			day: d.getDate()
		});

		//jQuery example
		$('#simply-countdown-losange').simplyCountdown({
			year: d.getFullYear(),
			month: d.getMonth() + 1,
			day: d.getDate(),
			enableUtc: false
		});
	</script>
</body>

</html>