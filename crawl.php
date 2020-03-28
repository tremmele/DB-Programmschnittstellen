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
      //get text from html page
      preg_match_all('/>(.*?)</m', $html, $texts);
      return !empty($texts[1]) ? $texts[1] : FALSE;
    }
  }
}
?>
<?php

//function for insert new link in table site
function insertLink($link)
{
  //login parameter
  $dbUrl = "127.0.0.1";
  $dbUser = "root";
  $dbPassword = "";
  $dbName = "mydb";
  //timestamp is needed for table sites
  $timestamp = date('Y-m-d H:i:s');

  // Connect to DB
  $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  //check if link is already in DB
  $result = $mysqli->query("SELECT * FROM site WHERE link = \"$link\"");

  // check if sql was not vaild
  if (!$result) {
    return;
  }

  //insert link to DB if new word
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

//function for insert new word in table words
function inserWord($word)
{
  //login parameter
  $dbUrl = "127.0.0.1";
  $dbUser = "root";
  $dbPassword = "";
  $dbName = "mydb";

  //Connect to DB
  $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  //Check if word is already in DB
  $result = $mysqli->query("SELECT * FROM words WHERE word = \"$word\"");

  // check if sql was not vaild
  if (!$result) {
    return;
  }

  //insert word to db if new word
  if (is_null($result->fetch_assoc())) {
    $insert_stmt = $mysqli->prepare("INSERT INTO words (word) VALUES (?)");
    $insert_stmt->bind_param('s', $word);
    $insert_stmt->execute();
    $mysqli->close();
  } else {
    $mysqli->close();
  }
  return;
}

//Connect word and site in junction table
function connectWordSite($word, $link)
{
  //login parameter
  $dbUrl = "127.0.0.1";
  $dbUser = "root";
  $dbPassword = "";
  $dbName = "mydb";

  //Connect do DB
  $mysqli = new mysqli($dbUrl, $dbUser, $dbPassword, $dbName);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  //get word and sideid
  $siteid = $mysqli->query("SELECT id FROM site WHERE link = \"$link\"");
  $wordid = $mysqli->query("SELECT id FROM words WHERE word = \"$word\"");
  
  // check if sql was not vaild
  if (!$siteid || !$wordid) {
    return;
  }

  //convert sql object to string
  $site_id = $siteid->fetch_assoc()['id'];
  $word_id = $wordid->fetch_assoc()['id'];

  //check if relation is already in table
  $result = $mysqli->query("SELECT * FROM words_sites WHERE words_id = $word_id AND site_id = $site_id");

  // check if sql was not vaild
  if (!$result) {
    return;
  }

  //insert relation if not exists
  if (is_null($result->fetch_assoc())) {
    $insert_stmt = $mysqli->prepare("INSERT INTO words_sites (words_id, site_id) VALUES (?,?)");
    $insert_stmt->bind_param('ii', $word_id, $site_id);
    $insert_stmt->execute();
    $mysqli->close();
  } else {
    $mysqli->close();
  }
  return;
}

//function for starting webcrawler
function startCrawler($uri, $rekursiv)
{
  insertLink($uri);
  $crawl = new Crawler($uri);

  //get text from site
  $plaintext = "";
  foreach ($crawl->get("texts") as $text) {
    //ignore empty results
    if ($text) {
      //reformat text 
      $plaintext = $plaintext . " " . $text;
    }
  }
  //split given text into words
  $words = preg_split('/ /', $plaintext);

  //insert results to DB
  foreach ($words as $word) {
    inserWord($word);
    connectWordSite($word, $crawl->base);
  }

  //get links from site
  foreach ($crawl->get("links") as $link) {

    //resolve relative links to absolute
    if (substr($link, 0, 4) !== "http") {
      $link = $crawl->base . $link;
    }

    insertLink($link);

    //start crawler recursivly
    if ($rekursiv) {
      startCrawler($link, true);
    }
  }
}

?>