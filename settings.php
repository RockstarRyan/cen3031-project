<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <!-- Bootstrap CSS (CDN) -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<!-- Custon css-->
    <link rel="stylesheet" href="css/mainStyle.css">
</head>
<body>
    <div class="container-fluid">
	<!-- set up bootstrap grid system to make page format responsive -->
	    <div class="row">
			<!-- top right settings-->
			<div class="col-2 text-start">
				<a href="main.php">
				<img src="images/BackSymbol.png" alt="Back Symbol" class="settings-icon mt-3" style="width: 50px; height: 50px;">
				</a>
			</div>
			<!-- places at the top center-->
			<div class="col-10">
				<p class="label mainFont text-center">Settings</p>
			</div>
			<div class="w-100"></div>
			<div class="col-9">
				<p class="label subFont">See our FAQ for Frequently Asked Questions and Guides</p>
				
			</div>
			<div class="col-2 text-end">
				<a href="faq.php">
				<button class="btn btn-primary big-button">FAQ</button>
				</a>
			</div>
		</div>
    </div>
	<!-- Bootstrap JavaScript (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JavaScript -->
    <script src="js/scripts.js"></script>
</body>
</html>
