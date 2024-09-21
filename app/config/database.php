<?php
/*
 * -------------------------------------------------------------------
 * DATABASE CONNECTIVITY SETTINGS
 * Enable this config file by adding it to: public/index.php // Build front-loader
 * -------------------------------------------------------------------
 * This file will contain the settings needed to access your database.
 *
 * -------------------------------------------------------------------
 * EXPLANATION OF VARIABLES
 * -------------------------------------------------------------------
 * DB_DRIVER    The database type. Currently Supported: mysql, mysqli, sqlite
 * DB_HOSTNAME  The hostname of your database server
 * DB_DATABASE  The name of the database you want to connect to
 * DB_USERNAME  The username used to connect to the database
 * DB_PASSWORD  The password used to connect to the database
 * -------------------------------------------------------------------
 */
define('DB_DRIVER', 'mysql'); // mysql, mysqli, sqlite are options for use with the Base Model.
define('DB_HOSTNAME', $_ENV['DB_HOST']);
define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
