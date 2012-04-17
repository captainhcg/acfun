<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'qwert03');
define('DB_NAME', 'acfun');

define('HOME_PATH', '/acfun');
define('CONTROLLER_PATH', 'controller');
define('VIEW_PATH', 'view');
define('MODULE_PATH', 'module');
define('JS_PATH', 'js');
define('CSS_PATH', 'style');
define('IMAGE_PATH', 'images');
define('CACHE_PATH', 'cache');

define('ERROR_LOG', 'error_log');

global $con;
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con)
    die("Can't connect to database<br>");

$db_selected = mysql_select_db(DB_NAME);
if (!$db_selected)
    die("Can't select database<br>");

mysql_query("set names 'utf8'"); 
