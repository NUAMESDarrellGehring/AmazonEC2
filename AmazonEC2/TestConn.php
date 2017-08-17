<?php
$servername = "34.212.128.254";
$username = "root";
$password = "Skull711!";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

echo "connected successfully";

?>