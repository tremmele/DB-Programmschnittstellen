# Simple Webcrawler

## crawl.php
The Crawler will get all links and words from a given website and inserts them into the sql tables. 
In most cases the crawler works with recursion and calls himself for each found link on the website.
Nevertheless when a new website is added by the user the crawler starts running without recurision.

## Database
The Database contains three tables site, word and sites_words:
* site -> contains all url the crawler founds + timestamp of the last crawl
* word -> all found words will be stored in this table
* sites_words -> the information which words has been found on which site can be found in here

## worker.php
The worker runs in the backround within a loop. It will start the crawler foreach link in the database whose timestamp is older than 24 h.

## frontend/index.php
The main site witch calls the needed function.

## frontend/functions/addUrl.php

## fronted/functions/search.php
