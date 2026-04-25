<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'smart_home';  // Yahan underscore sahi hai

$conn = mysqli_connect($host, $user, $password, $database);

if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Kolkata');
?>