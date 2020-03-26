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
                // Create first hit
                if (count($hitlist) == 0)
                {
                    $hitlist[] = new hit($row['link'], 1);
                    
                }
                else
                {
                    $hitExists = false;
                    foreach ($hitlist as $hit)
                    {
                        // increment word count if hit already exists
                        if($hit->url == $row['link'])
                        {
                            $hitExists = true;
                            // increment count of hit
                            $hit->count ++;
                        }
                    }
                    // create new hit if hit not exists
                    if (!$hitExists)
                    {
                         $hitlist[] = new hit($row['link'], 1);
                    }
                }
            }

        }
        // sort results
        rsort($hitlist);
        // print results
        echo "<hr>";
        echo "<h2>Suchergebnisse</h2>";
        echo "<ul>";
        foreach ($hitlist as $hit)
        {
            echo "<li>Seite gefunden. Es sind " . $hit->count . " Wörter der Suchanfrage enthalten. <br>";
            echo "Link: <a href=" . $hit->url . ">" . $hit->url . "</a></li><br>"; 
        }
        echo "</ul>";
    }

?>