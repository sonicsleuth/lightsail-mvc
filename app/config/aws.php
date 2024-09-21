<?php
/*
* -------------------------------------------------------------------
* AWS S3 Storage Service Settings
* Enable this config file by adding it to: public/index.php // Build front-loader
* -------------------------------------------------------------------
*/
define('AWS_BUCKET_NAME', $_ENV['AWS_BUCKET_NAME']);
define('AWS_REGION', $_ENV['AWS_REGION']);
define('AWS_VERSION', $_ENV['AWS_VERSION']);
define('AWS_ACCESS_KEY', $_ENV['AWS_ACCESS_KEY']);
define('AWS_SECRET_KEY', $_ENV['AWS_SECRET_KEY']);