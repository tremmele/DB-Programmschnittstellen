<html>
    <head>
        <title>Suchmaschine</title>
    </head>
    <body>
        <h1> Suchmaschine</h1>

    <?php
        //includes
        include "functions/addUrl.php";


        // Add URL to DB 
        echo "<form action=\"\" method=\"post\">";
            echo "<label for=\"url\">URL:</label>";
            echo "<input type=\"text\" id=\"url\" name=\"url\"><br><br>";
            echo "<input type=\"submit\" value=\"hinzufügen\">";
        echo "</form>";

        if(isset($_POST['url']))
        {
            // Add URL
            addUrl($_POST['url']);
            echo "Die URL " . $_POST['url'] . " wurde hinzugefügt";
        }

        // Search window
        
        // Print search results  

    ?>
    </body>
</html>