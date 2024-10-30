<?php
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database";

    $conn = new mysqli($servername, $username, $password,   
 $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"]   
 == "GET") {
        // Fetch data from the database and return as JSON
        // ...
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update data in the database
        // ...
    }

    $conn->close();
?>