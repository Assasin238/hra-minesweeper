<?php

$hostName = "localhost";
$dbUser = "root"; //max přepsat na jméno u db
$dbPassword = ""; //přepsat když u vyšinky
$dbName = "harnachp_login_register"; //jméno db, pak je users pro kolonky; id, nick_name, email, password; další kolonka pak je leaderboard - víc v todo.txt
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong:");
}
?>