<?php
    function addUrl($url)
    {
        // check if input variable is a valid URL
        if(!filter_var($url, FILTER_VALIDATE_URL))
        {
            echo "Ungültige Eingabe";
            return;
        } 
        
        // connect to db
        $dbUrl = "127.0.0.1";
        $dbUser = "root";
        $dbPassword = "";
        $dbName = "mydb";
        $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
        if ($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        // check if site already exists
        $result = $mysqli->query("SELECT * FROM site WHERE link = \"$url\"");
        if ($result->num_rows != 0)
        {
            echo "Die Seite " . $url . " ist schon bekannt"; 
        } 
        else 
        {
            // add Url to db and set timestamp
            $timestamp = date('Y-m-d H:i:s');
            $insert_stmt = $mysqli->prepare("INSERT INTO site (link, time_stamp) VALUES (?, ?)");
            $insert_stmt->bind_param('ss', $url, $timestamp);
            $insert_stmt->execute();
            echo "Die URL " . $url . " wurde hinzugefügt";
            
            // start crawler

        }
    }
?>