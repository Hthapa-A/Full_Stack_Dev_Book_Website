<?php
$host = "localhost";
$user = "2415267";
$pass = "Bhubindra90@90@@";
$dbname = "db2415267";

$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) { die("Connection failed: " . $mysqli->connect_error); }
$mysqli->set_charset("utf8");
?>
