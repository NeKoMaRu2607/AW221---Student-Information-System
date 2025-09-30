<?php
    // Connection parameters
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "aw221_sis_project";
    
    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>