<!doctype html>
<html lang="english">
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<h3>Welcome</h3>

<p>Lightsail is working if you are seeing this page.</p>

<p>Lightsail is a ready-to-go <strong>Docker container</strong> that builds a <strong>LAMP Stack with PHP-8.2</strong></p>

<p>If you are looking for a <strong>Model-View-Controller (MVC) Framework</strong> for PHP <a href="https://github.com/sonicsleuth/lightsail-mvc">clone the repo on GitHub</a> </p>

<p>Begin learning how to implement Lightsail by reading the <a href="/docs">documentation</a>.</p>

<p>Learn how to implement:</p>
<ul>
    <li><a href="/docs#requirements">Requirements</a></li>
    <li><a href="/docs#start">Getting Started</a></li>
    <li><a href="/docs#setup">Setting up the Database</a></li>
    <li><a href="/docs#features">Features</a></li>
    <li><a href="/docs#models">Models</a></li>
    <li><a href="/docs#controllers">Controllers</a></li>
    <li><a href="/docs#views">Views</a></li>
    <li><a href="/examples/passing_data">Passing Values from Controllers to Views</a></li>
    <li><a href="/docs#sessions">Session Management</a></li>
    <li><a href="/docs#routing">URL Routes</a></li>
    <li><a href="/docs#logs">Message Logs</a></li>
    <li><a href="/docs/language">Language Dictionaries</a></li>
    <li><a href="/docs/api">API Interfaces</a></li>
    <li><a href="/docs/redis">Using Redis</a></li>
    <li><a href="/docs/aws_s3">Using AWS S3 Storage Service</a></li>
    <li><a href="/docs/geocoder">US Census GeoCoder API</a></li>
</ul>

<h3>PHP Information:</h3>

<p>Review all <a href="/home/phpinfo">PHP Configurations and Installed Libraries</a></p>

<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>