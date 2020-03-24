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

$dbUrl = "127.0.0.1";
$dbUser = "root";
$dbPassword = "";
$dbName = "mydb";
$crawlInterval = 1000;//86400;

//while(true)
//{
  // Connect to DB
  $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
  if ($mysqli->connect_errno)
  {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

#foreach($links as $l) {
#  if (substr($l,0,7)!='http://')
#    echo "<br>Link: $crawl->base/$l";
#}

$crawl = new Crawler('http://www.heidenheim.dhbw.de');
#echo implode($crawl -> get("links"));

foreach($crawl -> get("links") as $link) {
  
  #$sql = "INSERT INTO site (link, timestamp) VALUES ($link, date('Y-m-d H:i:s'))";
  $timestamp = date('Y-m-d H:i:s');
  $insert_stmt = $mysqli->prepare("INSERT INTO site (link, time_stamp) VALUES (?,?)");
  $insert_stmt->bind_param('ss', $link, $timestamp);
  $insert_stmt->execute();

}
$mysqli->close();
?>
</body>
</html>

