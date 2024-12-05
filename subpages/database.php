<?php

$hostName = "localhost";
$dbUser = "harnachp";
$dbPassword = "";
$dbName = "harnachp_login_register";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong:");
}
?>