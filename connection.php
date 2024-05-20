<?php
    $databaseName='powerdb';
    $username='root';
    $hostname='localhost';
    $port=3306;
    $password='';
    $conn = new mysqli($hostname, $username, $password, $databaseName , $port); // Adjust as per your database details

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

