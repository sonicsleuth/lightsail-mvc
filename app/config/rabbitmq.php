<?php
/*
* -------------------------------------------------------------------
* RABBIT-MQ CONNECTIVITY SETTINGS
* Enable this config file by adding it to: public/index.php // Build front-loader
* -------------------------------------------------------------------
*/
define('RABBITMQ_HOST', $_ENV['RABBITMQ_HOST']);
define('RABBITMQ_PORT', $_ENV['RABBITMQ_PORT']);
define('RABBITMQ_DEFAULT_USER', $_ENV['RABBITMQ_DEFAULT_USER']);
define('RABBITMQ_DEFAULT_PASS', $_ENV['RABBITMQ_DEFAULT_PASS']);
