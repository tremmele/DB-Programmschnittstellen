<html>
    <head>
        <title>Suchmaschine</title>
    </head>
    <body>
        <h1> Suchmaschine</h1>

    <?php
        //includes
        include "functions/addUrl.php";
        include "functions/search.php";


        // Add URL Form
        echo "<h2>URL hinzufügen</h2>"; 
        echo "<form action=\"\" method=\"post\">";
            echo "<label for=\"url\">URL:</label>";
            echo "<input type=\"text\" id=\"url\" name=\"url\"><br><br>";
            echo "<input type=\"submit\" value=\"hinzufügen\">";
        echo "</form>";
        echo "<hr>";
        // Add URL to DB
        if(isset($_POST['url']))
        {
            // Add URL
            addUrl($_POST['url']);
            echo "Die URL " . $_POST['url'] . " wurde hinzugefügt";
        }

        // Search Form
        echo "<h2>Suche</h2>";
        echo "<form action=\"\" method=\"post\">";
            echo "<label for=\"searchQuery\">Suchanfrage:</label>";
            echo "<input type=\"text\" id=\"searchQuery\" name=\"searchQuery\"><br><br>";
            echo "<input type=\"submit\" value=\"suchen\">";
        echo "</form>";
        
        // Print search results
        echo "<hr>";
        echo "<h2>Suchergebnisse:</h2>";
        if(isset($_POST['searchQuery']))
        {
            echo "<h3>Suchanfrage: " . $_POST['searchQuery'] . "</h3><br><br>";
            search($_POST['searchQuery']);
        }
    ?>
    </body>
</html>