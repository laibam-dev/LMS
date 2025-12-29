<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lms";  // <-- change this from 'polymath_lms' to 'lms'

$conn = new mysqli($host, $user, $password, $dbname);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>
