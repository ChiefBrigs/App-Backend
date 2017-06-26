<?php

/*
 * All database connection variables
 */

ob_start();
session_start();
error_reporting(0);
return $_Config = array(
    'DB_SERVER' => 'localhost',// db server
    'DB_USER' => 'root',// db user
    'DB_PASSWORD' => 'root',// db password (mention your db password here)
    'DB_NAME' => 'whatsClone',// database name
    'DB_TABLE_PREFIX' => 'wa_'//database prefix
);