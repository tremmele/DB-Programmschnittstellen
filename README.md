# Simple Webcrawler

## Setup
1. Start your local apache and mysql Server e.g. xampp
2. Clone this repository into your htdocs directory and move the crawl.php to the php/pear directory.
3. Run the SQL script in /db/search_db.sql to setup your database
4. Navigate with your browser to the index.php and insert one URL
5. start the worker.php on your system

## Structure
### crawl.php
The Crawler will get all links and words from a given website and inserts them into the sql tables. 
In most cases the crawler works with recursion and calls himself for each found link on the website.
Nevertheless when a new website is added by the user the crawler starts running without recurision.

### Database
The Database contains three tables site, word and sites_words:
* site -> contains all url the crawler founds + timestamp of the last crawl
* word -> all found words will be stored in this table
* sites_words -> the information which words has been found on which site can be found in here

### worker.php
The worker runs in the backround within a loop. It will start the crawler foreach link in the database whose timestamp is older than 24 h.

### frontend/index.php
The main site witch calls the needed functions and print the input forms.

### frontend/functions/addUrl.php
Checks if the input URL is valid and not exitsting in the database. After that it calls the crawler without recursion.

### fronted/functions/search.php
This function executes the search Requests against the database. The results are ranked on the number of matching words from the search request.

