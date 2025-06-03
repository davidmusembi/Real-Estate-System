<?php
$host='localhost'; $db='realestate'; $user='root'; $pass='toor';
$conn = new mysqli($host,$user,$pass,$db);
if ($conn->connect_error) die('DB Error: '.$conn->connect_error);
?>
