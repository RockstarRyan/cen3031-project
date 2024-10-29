<?php

include_once("includes/site_construct.inc");

// User is already logged in
if ((isset($_SESSION['UserName'])) && (isset($_SESSION['UserPassword']))) {
	redirect('main.php');
}

// User has just submitted login information (signed up)
if ((isset($_POST['UserName'])) && (isset($_POST['UserPassword'])) && (isset($_POST['ConfirmPassword'])) && (isset($_POST['UserFirstName'])) && (isset($_POST['UserLastName']))) {
    $columns = [['UserName','text','NO'],['UserPassword','text/password','NO'],['UserFirstName','text','NO'],['UserLastName','text','NO'],['UserEmail','text','YES']];
	$row_data = [];
	foreach ($columns as $column) {
		if (isset($_POST[$column[0]])) {
			$row_data[$column[0]] = $_POST[$column[0]];
		}
	}
	$row_data['UserName'] = strtolower($row_data['UserName']);

    if ($row_data['UserPassword'] == $_POST['ConfirmPassword']) {
        $result = $db->insertOne('users',$row_data,false,true);
        //echo var_dump($row_data).var_dump($user_id);
        if (!is_nan($result)) {
            $row_data['UserID'] = $result;
            foreach ($row_data as $index=>$field) {
                $_SESSION[$index] = $field;
            }

            redirect('main.php?first_login=true');
            exit;
        } else {
            $GLOBALS['sign_up_error'] = $result;
        }
    } else {
        $GLOBALS['sign_up_error'] = "passwords do not match";
    }
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
    <link rel="stylesheet" href="css/mainStyle.css"></head>
<body>
    <div class="login-container">
        <h1>Log In</h1>
        <p1>Start managing your medications today</p1>
        <img src = "images/pill-logo.png" alt = "pill">
        <nav> 
            <li><a href="login.php">Sign In</a></li> <!-- direct to login/sign up pages -->
            <li><a href="signup.php">Sign Up</a></li>
        </nav>
        <?php if (isset($GLOBALS['sign_up_error']) && $GLOBALS['sign_up_error']!==false) {
            echo "<p class='warning'>Error: ".$GLOBALS['sign_up_error']."</p>";
        } ?>
        <form method="post">
            <label for="UserName"><b>Username</b></label>
            <input type="text" placeholder="Type here..." name="UserName" required></input>
            <label for="UserPassword"><b>Password</b></label>
            <input type="password" placeholder="Type here..." name="UserPassword" required></input>
            <label for="ConfirmPassword"><b>Confirm Password</b></label>
            <input type="password" placeholder="Type here..." name="ConfirmPassword" required></input>
            <label for="UserEmail"><b>Email</b></label>
            <input type="text" placeholder="Type here..." name="UserEmail"></input>
            <label for="UserFirstName"><b>First Name</b></label>
            <input type="text" placeholder="Type here..." name="UserFirstName" required></input>
            <label for="UserLastName"><b>Last Name</b></label>
            <input type="text" placeholder="Type here..." name="UserLastName" required></input>
            <button type='submit'>Sign Up</button>
        </form>
    </div>
	<!-- Bootstrap JavaScript (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JavaScript -->
    <script src="js/scripts.js"></script></body>
</html>