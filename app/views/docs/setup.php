<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP :: Setting up the Database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<?php
if(!empty($data) && $data['status']) {
    echo "<div style='background: yellow; font-size: 2em; padding: 10px; margin-bottom: 25px;'>The MySQL database setup was a ".$data['status']."</div";
}
?>

<p><a href="/docs">Go Back</a></p>

<h2>Setting up the database</h2>

<p>First, let us have a look at the environment values that were loaded when you started up the Docker Container.<br>
    You can find these values in the <strong>/app/docker-compose.yml</strong> file.</p>

<pre><code class="language-php">
<?php
echo "MYSQL_HOST: " . $_ENV['DB_HOST'] . PHP_EOL;
echo "MYSQL_DATABASE: " . $_ENV['DB_DATABASE'] . PHP_EOL;
echo "MYSQL_USERNAME: " . $_ENV['DB_USERNAME'] . PHP_EOL;
echo "MYSQL_PASSWORD: " . $_ENV['DB_PASSWORD'] . PHP_EOL;
echo "MYSQL_ROOT_PASSWORD: " . $_ENV['DB_PASSWORD'] . PHP_EOL;
?>
</code></pre>

<p>If you do not see any PHP errors appearing in the above panel, <strong>then you are safe to continue.</strong></p>

<h3>Database Migrations</h3>

<p>Click the following link and <strong>Lightsail will create the required database tables</strong> and populate those tables with data
for the purpose of working through the documentation.</p>

<p><strong>Click this link:</strong> <a href="http://localhost/setup/migrateUp">Run Database Setup</a></p>

<p><strong>If all goes well,</strong> you shall see a confirmation at the top of this page when it reloads.</p>

<h3>What is the Migration doing you ask...</h3>

<p>The above Migration will execute the following SQL statements:</p>

<p><strong>Make Users table...</strong></p>
<pre><code class="language-sql">
CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL
            );

INSERT INTO users (username, password, role)
VALUES
('john_doe', 'password123', 'admin'),
('jane_smith', 'pass456', 'user'),
('bob_jones', 'qwerty789', 'user');
</code></pre>

<br>
<p><strong>Make Logs table...</strong></p>
<pre><code class="language-sql">
CREATE TABLE IF NOT EXISTS logs
(
    event_type     varchar(45)                        NULL,
    event_message  varchar(255)                       NULL,
    event_script   varchar(255)                       NULL,
    event_datetime datetime DEFAULT CURRENT_TIMESTAMP NULL
);

INSERT INTO logs (event_type, event_message, event_script )
VALUES
('error', 'MyController.php', 'MyController::get - Failed to get records.'),
('success', 'MyController.php', 'MyController::set - Successful operation.'),
('warning', 'MyController.php', 'MyController::update - Process took 2000ms.');
</code></pre>

<br>
<p><strong>Make Sessions table...</strong></p>
<pre><code class="language-sql">
CREATE TABLE IF NOT EXISTS sessions (
        session_id CHAR(32) NOT NULL,
        session_data TEXT NOT NULL,
        session_lastaccesstime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (session_id)
        );
</code></pre>

<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>