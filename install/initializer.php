<?php



// include the config.php file
include '../config/Config.php';
// include the database connection class
include '../config/DataBase.php';
// include the Helper class
include '../application/helpers/Helper.php';

$_DB = new DataBase($_Config);
$_DB->connect();
$_DB->selectDB();
$_GB = new Helper($_DB);
