<?php
$servername = "localhost";
$username = "root";
$password = "skull71";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

echo "connected successfully";

?>