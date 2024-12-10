<?php

$hostName = "localhost";
$dbUser = "root"; //max přepsat na jméno u db
$dbPassword = ""; //přepsat když u vyšinky
$dbName = "harnachp_login_register"; //jméno db pak je users pro kolonky
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong:");
}
?>