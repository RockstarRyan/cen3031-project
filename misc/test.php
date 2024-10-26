<?php

// Connect to MySQL DB
$db = new mysqli('cen3031.rockstarryan.net','ggtesite_rr_cen3031','v&Z@^pXwn#!CDWfd2j','ggtesite_rr_cen3031',3306);

if (mysqli_connect_errno()) {
	echo "<p>Error: Could not connect to database. Please try again later.</p>"; exit;
} else {
    echo "<p>Success</p>";
}