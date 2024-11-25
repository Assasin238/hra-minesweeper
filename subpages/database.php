<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "";
$conn = mysqli_connect($hostName, $dbUSer, $dbPassword);
if (!$conn) {
    die("Something went wrong:");
}
?>