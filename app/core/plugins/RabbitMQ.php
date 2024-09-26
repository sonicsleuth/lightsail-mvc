<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * An abstraction for RabbitMQ - https://www.rabbitmq.com
 *
 * Local RabbitMQ Manager - http://localhost:15672
 *
 *  HOW IT WORKS:
 *
 *  Producer: The publish() function sends a message to the specified routing_key with the given message_body.
 *
 *  Consumer: The consume() function listens to the queue for specific routing keys. In the example above,
 *  it's bound to all messages that match the pattern user.*.
 *
 *  Topic Exchange: Topic exchanges route messages based on routing patterns (e.g., user.created or user.updated).
 *  Consumers can subscribe to messages by providing specific patterns (e.g., user.* to capture all user-related events).
 *
 * HOW TO USE:
 *
 * Producer Example ------------------------------------------
 * You can use this class to publish messages to RabbitMQ using topic routing. Here’s an example of how to send a message:
 *
 * $rabbit = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest');
 *
 * // Publish a message with the routing key 'user.created'
 * $rabbit->publish('user.created', json_encode(['user_id' => 123, 'name' => 'John Doe']));
 *
 * // Close the connection
 * $rabbit->close();
 *
 * Consumer Example ------------------------------------------
 * You can also consume messages from specific routing keys:
 *
 * $rabbit = new RabbitMQ('rabbitmq', 5672, 'guest', 'guest');
 *
 * // Define the callback to handle the consumed message
 * $callback = function($msg) {
 * echo 'Received: ' . $msg->body . "\n";
 * // You can decode and process the message here
 * };
 *
 * // Consume messages from the 'user_queue' bound to the 'user.*' routing key
 * $rabbit->consume('user_queue', ['user.*'], $callback);
 *
 * // Close the connection after consuming
 * $rabbit->close();
 */

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{
    private $connection;
    private $channel;
    private $exchange;
    private $queue;

    public function __construct($host, $port, $user, $pass, $exchange = 'topic_exchange')
    {
        $this->exchange = $exchange;

        // Establish connection to RabbitMQ server
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();

        // Declare a topic exchange
        $this->channel->exchange_declare($this->exchange, 'topic', false, true, false);
    }

    /**
     * Publish a SINGLE message to the RabbitMQ server.
     *
     * @param string $routing_key The routing key for the message
     * @param string $message_body The payload to be delivered
     */
    public function publish(string $routing_key, string $message_body): void
    {
        $message = new AMQPMessage($message_body, ['content_type' => 'text/plain']);
        $this->channel->basic_publish($message, $this->exchange, $routing_key);
    }

    /**
     * Consume a SINGLE messages from the RabbitMQ server. First-In First-Out from the queue.
     *
     * @param string $queue The queue to consume from
     * @param array $binding_keys Array of routing keys to bind to
     * @param callable $callback The function to call when a message is consumed
     */
    public function consume(string $queue, array $binding_keys, callable $callback, string &$status_messages): void
    {
        // Declare the queue
        $this->channel->queue_declare($queue, false, true, false, false);

        // Bind the queue to the exchange with the routing keys
        foreach ($binding_keys as $binding_key) {
            $this->channel->queue_bind($queue, $this->exchange, $binding_key);
        }

        // Consume messages from the queue
        $this->channel->basic_consume($queue, '', false, false, false, false, function($msg) use ($callback, $status_messages) {
            // Process the message
            $callback($msg);

            // Stop if there are no more callbacks
            if ($this->channel->callbacks) {
                $this->channel->basic_cancel($msg->delivery_info['consumer_tag']);
            }
        });

        // If the queue is empty, hang up the connection. If any errors, inform the consumer callback.
        while ($this->channel->is_consuming()) {
            try {
                // Timeout of 5 seconds, Add timeout to avoid connection from hanging.
                $this->channel->wait(null, false, 5);
            } catch (PhpAmqpLib\Exception\AMQPTimeoutException $e) {
                $status_messages .= "[INFO] No messages received within the timeout period, stopping consumer...\n";
                break;
            } catch (Exception $e) {
                $status_messages .= 'Error during consumption: ' . $e->getMessage();
                break;
            }
        }
    }

    /**
     * Close the connection and channel
     */
    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}