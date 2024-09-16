<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * An abstraction for REDIS - https://redis.io
 *
 * HOW TO USE:
 * // Create an instance of the Redis abstraction
 * $redis = new RedisAbstraction();
 *
 * // Set a value in Redis
 * $redis->set('name', 'John Doe');
 *
 * // Get a value from Redis
 * $name = $redis->get('name');
 * echo $name; // Output: John Doe
 *
 * // Check if a key exists
 * if ($redis->exists('name')) {
 * echo 'Key exists!';
 * }
 *
 * // Delete a key
 * $redis->delete('name');
 */

class RedisModel {
    private $redis;
    private $host;
    private $port;

    public function __construct($host = REDIS_HOST, $port = REDIS_PORT)
    {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    // Method to establish a connection to Redis
    public function connect()
    {
        try {
            $this->redis = new Redis();
            $this->redis->connect($this->host, $this->port);
        } catch (Exception $e) {
            echo "Could not connect to Redis: ", $e->getMessage();
        }
    }

    // Create or Update a value in Redis
    public function set($key, $value)
    {
        return $this->redis->set($key, $value);
    }

    // Read a value from Redis
    public function get($key)
    {
        return $this->redis->get($key);
    }

    // Delete a value from Redis
    public function delete($key)
    {
        return $this->redis->del($key);
    }

    // Check if a key exists in Redis
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    // List all keys (optional helper method)
    public function keys($pattern = '*')
    {
        return $this->redis->keys($pattern);
    }
}
