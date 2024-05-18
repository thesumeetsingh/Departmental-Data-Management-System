<?php
    $databaseName='powerdb';
    $conn = new mysqli('localhost', 'root', '', $databaseName , 3306); // Adjust as per your database details

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //include 'connection.php';


?>

