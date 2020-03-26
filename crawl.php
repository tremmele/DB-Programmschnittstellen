<?php
class Crawler
{

  protected $markup = '';
  public $base = '';

  public function __construct($uri)
  {
    $this->base = $uri;
    $this->markup = $this->getMarkup($uri);
  }

  public function getMarkup($uri)
  {
    return file_get_contents($uri);
  }

  public function get($type)
  {
    $method = "_get_{$type}";
    if (method_exists($this, $method)) {
      return call_user_func(array($this, $method));
    }
  }

  protected function _get_images()
  {
    if (!empty($this->markup)) {
      preg_match_all('/<img([^>]+)\/>/i', $this->markup, $images);
      return !empty($images[1]) ? $images[1] : FALSE;
    }
  }

  protected function _get_links()
  {
    if (!empty($this->markup)) {
      //preg_match_all('/<a([^>]+)\>(.*?)\<\/a\>/i', $this->markup, $links);
      preg_match_all('/href=\"(.*?)\"/i', $this->markup, $links);
      return !empty($links[1]) ? $links[1] : FALSE;
    }
  }

  protected function _get_texts()
  {
    if (!empty($this->markup)) {
      //remove java script tags
      $html = preg_replace('/<script>(.*?)<\/script>/m', "", $this->markup);
      preg_match_all('/>(.*?)</m', $html, $texts);
      return !empty($texts[1]) ? $texts[1] : FALSE;
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

  function insertLink($link)
  {
    $dbUrl = "127.0.0.1";
    $dbUser = "root";
    $dbPassword = "";
    $dbName = "mydb";
    $timestamp = date('Y-m-d H:i:s');
    // Connect to DB
    $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $timestamp = date('Y-m-d H:i:s');
    $result = $mysqli->query("SELECT * FROM site WHERE link = \"$link\"");

    //insert found URL to database
    if (is_null($result->fetch_assoc())) {
      $insert_stmt = $mysqli->prepare("INSERT INTO site (link, time_stamp) VALUES (?,?)");
      $insert_stmt->bind_param('ss', $link, $timestamp);
      $insert_stmt->execute();
      $mysqli->close();
    } else {
      $mysqli->close();
    }
    return;
  }

  function inserWord($word)
  {
    $dbUrl = "127.0.0.1";
    $dbUser = "root";
    $dbPassword = "";
    $dbName = "mydb";

    $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $result = $mysqli->query("SELECT * FROM words WHERE word = \"$word\"");
    //insert found word to database
    if (is_null($result->fetch_assoc())) {
      //insert word to db
      $insert_stmt = $mysqli->prepare("INSERT INTO words (word) VALUES (?)");
      $insert_stmt->bind_param('s', $word);
      $insert_stmt->execute();
      $mysqli->close();
    } else {
      $mysqli->close();
    }
 
    return;

  }

  function connectWordSite($word, $link)
  {
    $dbUrl = "127.0.0.1";
    $dbUser = "root";
    $dbPassword = "";
    $dbName = "mydb";
    //get word and sideid
    $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $siteid = $mysqli->query("SELECT id FROM site WHERE link = \"$link\"");
    $wordid = $mysqli->query("SELECT id FROM words WHERE word = \"$word\""); 
    
    $site_id =$siteid->fetch_assoc()['id'];
    $word_id =$wordid->fetch_assoc()['id'];

    $result = $mysqli->query("SELECT * FROM words_sites WHERE word_id = \"(int) $word_id\" AND site_id = \"(int) $site_id\"");

    if (is_null($result->fetch_assoc())) {
      $insert_stmt = $mysqli->prepare("INSERT INTO words_sites (word_id, site_id) VALUES (?,?)");
      $insert_stmt->bind_param('ii', $word_id, $site_id);
      $insert_stmt->execute();
      $mysqli->close();
    }
    else {
      $mysqli->close();
    }
  }

  function startCrawler($uri)
  {
    insertLink($uri);


    $crawl = new Crawler($uri);



    foreach ($crawl->get("links") as $link) {


      ////resolve relative links to absolute
      //if(substr( $link, 0, 4 ) !== "http") {
      //  $link = $crawl->base . $link;
      //}

      insertLink($link);

      //get word from site
      $plaintext = "";
      foreach ($crawl->get("texts") as $text) {
        //ignore empty results
        if ($text) {
          $plaintext = $plaintext . " " . $text;
        }
      }
      $words = preg_split('/ /', $plaintext);
      foreach ($words as $word) {
        inserWord($word);
        connectWordSite($word, $crawl->base);
      }

      //start crawler recursivly
      #startCrawler($link);
    }
  }

  ?>
</body>

</html>