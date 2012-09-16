<?php

// This page cannot be viewed, it must be included
if (!defined('IN_EZRPG'))
    exit;

// Start Session
session_start();
require_once('config.php');

// set up a character encoding.
ini_set('default_charset', 'UTF-8');
header('Content-Type: text/html; charset=UTF-8');

//Show errors?
(SHOW_ERRORS == 0) ? error_reporting(0) : error_reporting(E_ALL);

//Constants
define('CUR_DIR', realpath(dirname(__FILE__)));
define('MOD_DIR', CUR_DIR . '/modules');
define('ADMIN_DIR', CUR_DIR . '/admin');
define('LIB_DIR', CUR_DIR . '/lib');
define('EXT_DIR', LIB_DIR . '/ext');
define('HOOKS_DIR', CUR_DIR . '/hooks');

require_once(CUR_DIR . '/lib.php');

// Database
try {
    $db = DbFactory::factory($config['database']);
} catch (DbException $e) {
    echo $e->__toString();
	exit(1);
}

// Database connection is made, delete configuration.
unset($config['database']);

// Smarty
$tpl = new Smarty();
$tpl->template_dir = CUR_DIR . '/templates/default/';
$tpl->compile_dir  = CUR_DIR . '/templates/.compiled/';
$tpl->config_dir   = CUR_DIR . '/templates/.config/';
$tpl->cache_dir    = CUR_DIR . '/templates/.cache/';

// Initialize $player
$player = false;

// Create a hooks object
$hooks = new Hooks($db, $config, $tpl, $player);

// Include all hook files
$hook_files = glob(HOOKS_DIR . '/*.*.php');
foreach($hook_files as $hook_file) 
	require_once($hook_file);

//Run login hooks on player variable
$player = $hooks->run_hooks('player', 0);