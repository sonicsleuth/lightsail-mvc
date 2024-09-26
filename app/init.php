<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');
/*
 * Enable this INIT file by adding it to: public/index.php // Build front-loader
 */

// REQUIRED SERVICES
require_once 'core/App.php';                // Front-loader
require_once 'core/Controller.php';         // Base Controller
require_once 'core/Model.php';              // Base Model (PDO DB Abstraction)

// OPTIONAL SERVICES
require_once 'core/plugins/Redis.php';      // Redis Cache
require_once 'core/plugins/RabbitMQ.php';   // RabbitMQ Message Broker
require_once 'core/plugins/AWS_S3.php';     // AWS S3 File Manager
require_once 'core/plugins/Geocoder.php';   // US CENSUS Geocoder API