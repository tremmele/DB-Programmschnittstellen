<?php
  class Crawler {

    protected $markup = '';
    public $base = '';

    public function __construct($uri) {    
      $this->base = $uri;    
      $this->markup = $this->getMarkup($uri);

    }

    public function getMarkup($uri) {
      return file_get_contents($uri);
    }

    public function get($type) {
      $method = "_get_{$type}";
      if (method_exists($this, $method)){
        return call_user_func(array($this, $method));
      }
    }

    protected function _get_images() {
      if (!empty($this->markup)){
        preg_match_all('/<img([^>]+)\/>/i', $this->markup, $images); 
        return !empty($images[1]) ? $images[1] : FALSE;
      }
    }

    protected function _get_links() {
      if (!empty($this->markup)){
        //preg_match_all('/<a([^>]+)\>(.*?)\<\/a\>/i', $this->markup, $links);
        preg_match_all('/href=\"(.*?)\"/i', $this->markup, $links);
        return !empty($links[1]) ? $links[1] : FALSE;
      }
    }
  }
?>
<html>

<body>
<h2>Webcrawler</h2>
<?php

  $url = 'http://www.heidenheim.dhbw.de';
  startCrawler($url);

function startCrawler($uri) {


$crawl = new Crawler($uri);

foreach($crawl -> get("links") as $link) {
  $dbUrl = "127.0.0.1";
$dbUser = "root";
$dbPassword = "";
$dbName = "mydb";


  // Connect to DB
  $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
  if ($mysqli->connect_errno)
  {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  
  $timestamp = date('Y-m-d H:i:s');
  $result = $mysqli->query("SELECT * FROM site WHERE link = \"$link\"");

  if(is_null($result->fetch_assoc()))
  {
  $insert_stmt = $mysqli->prepare("INSERT INTO site (link, time_stamp) VALUES (?,?)");
  $insert_stmt->bind_param('ss', $link, $timestamp);
  $insert_stmt->execute();
  $mysqli->close();

  startCrawler($link);
  }
  else {
  $mysqli->close();
  }
}


}
?>
</body>
</html>

