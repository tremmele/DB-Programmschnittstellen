<?php
    // class for hit objects
    class hit {
            public $url;
            public $count;

            public function __construct($url, $count)
            {
                $this->url = $url;
                $this->count = $count;
            }
    }


    function search($searchQuery)
    {
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
    
        // cut query in single words
        $words = preg_split("/ /", $searchQuery);
        $hitlist = [];   
        foreach ($words as $word)
        {
            // get URLs From DB
            $result = $mysqli->query("SELECT s.* FROM site s INNER JOIN words_sites ws ON s.id = ws.site_id INNER JOIN words w ON w.id = ws.words_id WHERE w.word = \"$word\"");
            
            while ($row = $result->fetch_assoc()) 
            {
                if (count($hitlist) == 0)
                {
                    $hitlist[] = new hit($row['link'], 1);
                    
                }
                else
                {
                    $hitExists = false;
                    foreach ($hitlist as $hit)
                    {
                        if($hit->url == $row['link'])
                        {
                            $hitExists = true;
                            // increment count of hit
                            $hit->count ++;
                        }
                    }
                    if (!$hitExists)
                    {
                         // create new hit
                         $hitlist[] = new hit($row['link'], 1);
                    }
                }
            }

        }
        foreach ($hitlist as $hit)
        {
            // sort hits 

            // print results
            echo "<a href=http://" . $hit->url . ">" . $hit->url . "</a>" . "&emsp;Anzahl übereinstimmender Wörter:" . $hit->count . "<br>"; 
        }
    }

?>