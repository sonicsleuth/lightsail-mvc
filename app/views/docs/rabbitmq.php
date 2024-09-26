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

<h2>Using RabbitMQ</h2>

<p>Here’s an example of how you can use the RabbitMQ Model for producing and consuming messages with RabbitMQ (<a href="https://www.rabbitmq.com">https://www.rabbitmq.com</a>) using the TOPIC exchange.</p>
<p>This will require the php-amqplib library, so make sure to install it using Composer if you haven't already:</p>

<pre><code class="language-php">composer require php-amqplib/php-amqplib
</code></pre><br>

<h3>RabbitMQ Management</h3>

<p>Once RabbitMQ is up and running, you can access the Management Console here: <a href="http://localhost:15672">http://localhost:15672</a>
<br>Login using:
    <br>Username: <strong><?php echo RABBITMQ_DEFAULT_USER; ?></strong>
    <br>Password: <strong><?php echo RABBITMQ_DEFAULT_PASS; ?></strong></p>

<h3>Producer Example</h3>
You can use this example to publish messages to RabbitMQ using topic routing. Here’s an example of how to send a message:

<pre><code class="language-php">$rabbit = new RabbitMQ(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_DEFAULT_USER, RABBITMQ_DEFAULT_PASS);

// Publish a message with the routing key 'user.created'
$rabbit->publish('user.created', json_encode(['user_id' => 123, 'name' => 'John Doe']));

// Close the connection
$rabbit->close();
</code></pre><br>

<h3>Consumer Example</h3>
You can also consume messages from specific routing keys:

<pre><code class="language-php">$rabbit = new RabbitMQ(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_DEFAULT_USER, RABBITMQ_DEFAULT_PASS);

// Define the callback to handle the consumed message
$callback = function($msg) {
    echo 'Received: ' . $msg->body . "\n";
    // You can decode and process the message here
};

// Consume messages from the 'user_queue' bound to the 'user.*' routing key
$rabbit->consume('user_queue', ['user.*'], $callback);

// Close the connection after consuming
$rabbit->close();
</code></pre><br>

<h3>Example Output</h3>
<p>The following output was Produced and Consumed using the above examples as this page was generated.</p>

<pre><code class="language-text"><?php print_r($status_messages); ?></code></pre><br>

<h3>How It Works:</h3>
<ul>
    <li><strong>Producer:</strong> The publish() function sends a message to the specified routing_key with the given message_body.</li>
    <li><strong>Consumer:</strong> The consume() function listens to the queue for specific routing keys. In the example above, it's bound
        to all messages that match the pattern user.*.</li>
    <li><strong>Topic Exchange:</strong> Topic exchanges route messages based on routing patterns (e.g., user.created or user.updated).
        Consumers can subscribe to messages by providing specific patterns (e.g., user.* to capture all user-related events).</li>
</ul>


<strong>Notes:</strong>
<ul>
    <li>Make sure RabbitMQ is running and the necessary ports are open.</li>
    <li>The exchange type is set to topic to enable routing based on patterns.</li>
    <li>The queue is declared as durable, meaning that it will survive server restarts.</li>
</ul>

<h3>Troubleshooting</h3>

<p>If it seems to take an unusually long time for a response from RabbitMQ, check the logs:</p>
<pre><code class="language-text">run: docker logs lightsail-rabbitmq-container</code></pre><br>


<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>
