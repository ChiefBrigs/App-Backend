<?php
/**
 * Created by PhpStorm.
 * User: abderrahimelimame
 * Date: 9/17/16
 * Time: 04:04
 */


// include the database connection class
include 'config/DataBase.php';
// include the config file
include 'config/Config.php';
// include the SessionsController class
include 'application/controllers/SessionsController.php';
// include the UsersController class
include 'application/controllers/UsersController.php';
// include the MessagesController class
include 'application/controllers/MessagesController.php';
// include the GroupsController class
include 'application/controllers/GroupsController.php';
// include the ProfileController class
include 'application/controllers/ProfileController.php';
// include the Pagination class
include 'application/helpers/Pagination.php';
// include the Helper class
include 'application/helpers/Helper.php';
// include the Security class
include 'application/helpers/Security.php';


$_DB = new DataBase($_Config);
$_DB->connect();
$_DB->selectDB();
$Security = new Security($_DB);
$_GB = new Helper($_DB);
$Users = new UsersController($_GB);
$Messages = new MessagesController($_GB, $Users);
$Groups = new GroupsController($_GB);
$Profile = new ProfileController($_GB);

// some PHP
$base_url = array(
    'base_url' => $_GB->getSettings("base_url")
);
echo json_encode($base_url);
