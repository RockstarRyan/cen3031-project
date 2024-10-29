<?php

session_start();

error_reporting(E_ALL);

ini_set("allow_url_include", '1');
include_once("includes/mysqli_ext.inc");

// Connect to MySQL DB
$db = new mysqli_ext('cen3031.rockstarryan.net','ggtesite_rr_cen3031','v&Z@^pXwn#!CDWfd2j','ggtesite_rr_cen3031',3306);

if (mysqli_connect_errno()) {
	echo "<p>Error: Could not connect to database. Please try again later.</p>"; exit;
}

// Other functions
function redirect($url='') {
    header('Location: ./'.$url);
    exit;
}