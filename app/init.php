<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');
/*
 * * Enable this INIT file by adding it to: public/index.php // Build front-loader
 */
require_once 'core/App.php';            // Front-loader
require_once 'core/Controller.php';     // Base Controller
require_once 'core/Model.php';          // Base Model (PDO DB Abstraction)
require_once 'core/RedisModel.php';     // Redis Abstraction
require_once 'core/AWSS3Model.php';     // AWS S3 File Manager