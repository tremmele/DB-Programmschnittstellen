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
    $result = $mysqli->query("SELECT link FROM site");
    while ($row = $result->fetch_assoc()) 
    {
        // Print every URL
        echo "URL: " . $row['link'] . "\n";
    }

  //}

?>