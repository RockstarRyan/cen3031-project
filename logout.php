<?php

session_start();

session_unset();
session_destroy();

include_once("includes/site_construct.inc");

redirect('login.php');

exit;

?>
