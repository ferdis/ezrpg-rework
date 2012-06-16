<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Title: Config
  The most important settings for the game are set here.
*/

/*
  Variables: Database Connection
  Connection settings for the database.
  
  $config - Array containing database connection details.
  server - Database server
  database - Database name
  username - Username to login to server with
  password - Password to login to server with
  driver - Contains the database driver to use to connect to the database.
  prefix - Prefix for database tables
*/

$config = array(
    'server'    => 'localhost',
    'database'  => 'ezrpg',
    'username'  => 'root',
    'password'  => '',
    'prefix'    => 'ez_',
    'driver'    => 'mysqli'
);

/*
  Constant:
  This secret key is used in the hashing of player passwords and other important data.
  Secret keys can be of any length, however longer keys are more effective.
  
  This should only ever be set ONCE! Any changes to it will cause your game to break!
  You should save a copy of the key on your computer, just in case the secret key is lost or accidentally changed,.
  
  SECRET_KEY - A long string of random characters.
*/
define('SECRET_KEY', '/DmuUn7VZKz@1#W4)2g8e>u!');


/*
  Constants: Settings
  Various settings used in ezRPG.
  
  VERSION - Version of ezRPG
  SHOW_ERRORS - Turn on to show PHP errors.
  DEBUG_MODE - Turn on to show database errors and debug information.
*/
define('VERSION', '0.1');
define('SHOW_ERRORS', 0);
define('DEBUG_MODE', 0);
?>