<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP :: Using Redis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<p><a href="/docs">Go Back</a></p>

<h2>Using Redis</h2>

<p>Redis is a source-available, in-memory storage, used as a distributed, in-memory key–value database,
    cache and message broker, with optional durability, as described by <a href="https://redis.io">https://redis.io</a></p>

<p>Options for using Redis can be:</p>
<ul>
    <li>A cache mechanism for frequent database query results,</li>
    <li>a session database layer,</li>
    <li>a used as a persistent database storage layer,</li>
    <li>and many more.</li>
</ul>

<br>
<p>The following examples show how to implement <strong>Redis</strong> in your application,
    however, how you choose to use it is up to your imagination and business requirements.</p>

<pre><code class="language-php">
// Create an instance of the Redis abstraction
$redis = $this->model('RedisModel');

// Set a value in Redis
$redis->set('name', 'John Doe');

// Get a value from Redis
$name = $redis->get('name');
echo $name; // Output: John Doe

// Check if a key exists
if ($redis->exists('name')) {
    echo 'Key exists!';
}

// Delete a key
$redis->delete('name');
</code></pre>

<h3>For example:</h3>
<p>The following output was created using <strong>Redis</strong> while generating this page.
Have a look at the file <strong>/app/controllers/Docs.php</strong> and the function called <strong>redis()</strong></p>

<pre><code class="language-text">
<?php
echo "Value for NAME = ". $data['name'] . PHP_EOL;
echo "Value for DATE = ". $data['date'] . PHP_EOL;
?>
</code></pre>
*Note: the date above will change if you reload this page because we are updating the value of "date" in Redis.

<h3>Storing JSON objects:</h3>
<p>It is just as easy to store JSON object into Redis. Let's store some data for a user...</p>

<pre><code class="language-php">
// Create an instance of the Redis abstraction
$redis = $this->model('Redis');

// Create a sample JSON object (as an associative array in PHP)
$data = [
    "name" => "John Doe",
    "email" => "john@example.com",
    "age" => 30,
    "location" => "New York"
];

// Convert the array to a JSON string
$jsonData = json_encode($data);

// Store the JSON object in Redis under the key 'user:1001'
$redis->set('user:1001', $jsonData);

// Retrieve the JSON string from Redis
$storedJsonData = $redis->get('user:1001');

// Decode the JSON string back into a PHP array
$decodedData = json_decode($storedJsonData, true);

// Display the decoded data
echo "User Data from Redis:\n";
print_r($decodedData);
</code></pre>

<br>
<p>The following output was created using <strong>Redis</strong> while generating this page.
    Have a look at the file <strong>/app/controllers/Docs.php</strong> and the function called <strong>redis()</strong></p>

<pre><code class="language-text">
<?php
print_r($data['user'])
?>
</code></pre>

<h2>Values stored in Redis are not persisted across restarts </h2>

<p>By default, values stored in Redis are not persisted across restarts unless you explicitly configure Redis to
    persist data. Redis operates primarily as an in-memory data store, but it does offer two mechanisms for persistence:</p>
<ul>
    <li>RDB (Redis Database Backup)</li>
    <li>AOF (Append-Only File)</li>
</ul>

<br>
<p>You can learn more about implementing those solutions at <strong>Redis</strong> <a href="https://redis.io">https://redis.io</a></p>

<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>
