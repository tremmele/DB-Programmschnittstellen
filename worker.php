<?php
  include "crawl.php";
  
  $dbUrl = "127.0.0.1";
  $dbUser = "root";
  $dbPassword = "";
  $dbName = "mydb";


  //while(true)
  //{
    // Connect to DB
    $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
    if ($mysqli->connect_errno)
    {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // Get URLs From DB
    $result = $mysqli->query("SELECT * FROM site");
    while ($row = $result->fetch_assoc()) 
    {
        // Print every URL
        echo "ID: " . $row['id'] . " URL: " . $row['link'] . "\n";

        // Update timestamp
        $timestamp = date('Y-m-d H:i:s');
        $update_stmt = $mysqli->prepare("UPDATE site SET time_stamp = ? WHERE id = ?");
        $update_stmt->bind_param('si', $timestamp, $row['id']);
        $update_stmt->execute();
    }

  //}

?>