<?php

include_once("includes/site_construct.inc");

// User is already logged in
if ((isset($_SESSION['UserName'])) && (isset($_SESSION['UserPassword']))) {
	redirect('main.php');
}

// User has just submitted login information
if ((isset($_POST['UserName'])) && (isset($_POST['UserPassword']))) {
	$password_hash = md5($_POST['UserPassword']);
	$users = $db->query("SELECT `UserID`, `UserName`, `UserPassword`, `UserFirstName`, `UserLastName` FROM `users`")->fetch_all(MYSQLI_ASSOC);
	$user_name = strtolower($_POST['UserName']);
	foreach ($users as $user) {
		if ($user['UserName']==$user_name && $user['UserPassword']==$password_hash) {
			$fields = ['UserID','UserName','UserPassword','UserFirstName','UserLastName'];
			foreach ($fields as $field) {
				$_SESSION[$field] = $user[$field];
			}
			redirect('main.php');
		}
	}
	$GLOBALS['login_error'] = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
	<!-- Bootstrap CSS (CDN) -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<!-- Custon css-->
    <link rel="stylesheet" href="css/mainStyle.css">
</head>
<body>
    <div class="login-container">
        <h1>Log In</h1>
        <p1>Welcome back partner!</p1>
        <img src="images/pill-logo.png" alt="pill">
        <nav> 
            <li><a href="login.php">Sign In</a></li> <!-- direct to login/sign up pages -->
            <li><a href="signup.php">Sign Up</a></li>
        </nav>
        <form method="post">
            <label for="UserName"><b>Username</b></label>
            <input type="text" placeholder="Type here..." name="UserName" required></input>
            <label for="UserPassword"><b>Password</b></label>
            <input type="password" placeholder="Type here..." name="UserPassword" required></input>
            <button type="submit">Log In</button>
        </form>
    </div>
	<!-- Bootstrap JavaScript (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JavaScript -->
    <script src="js/scripts.js"></script></body>
</html>
