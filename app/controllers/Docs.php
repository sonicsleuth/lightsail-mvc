<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

class Docs extends Controller
{
    public function __construct()
    {
        $this->load_helper(['view']);
    }

    public function index(string $param1 = '', string $param2 = ''): void
    {
        $logsModel = $this->model('Log');
        $sessionsModel = $this->model('Session');

        $data = [
            "logs" => $logsModel->getLogs(),
            "sessions" => $sessionsModel->getSessionData(),
        ];

        $this->view('docs/index', $data);
    }

    public function session(): void
    {
        $this->model('Session');

        $_SESSION['fname'] = 'Walter';
        $_SESSION['lname'] = 'Smith';
        $_SESSION['title'] = 'Sales Manager';

        echo "<pre>";
        echo "GET ALL SESSION DATA:" . PHP_EOL;
        print_r($_SESSION);

    }

    public function language(): void
    {
        $this->view('docs/language');
    }

    public function redis(): void
    {
        $redis = new RedisModel(); // Directly accessible when installed as a core service here: app/init.php

        // Store some simple values:
        $redis->set('name', 'John Doe');
        $redis->set('date', date("Y-m-d H:i:s"));

        $name = $redis->get('name');
        $date = $redis->get('date');

        // Store a JSON object:
        // Create a sample JSON object (as an associative array in PHP)
        $data = [
            "name" => "John Doe",
            "email" => "john.doe@example.com",
            "age" => 30,
            "location" => "New York"
        ];

        $jsonData = json_encode($data);

        $redis->set('user:1001', $jsonData); // Store the JSON object in Redis under the key 'user:1001'

        $storedJsonData = $redis->get('user:1001');

        $user = json_decode($storedJsonData, true);

        $data = [
            'name' => $name,
            'date' => $date,
            'user' => $user
        ];

        $this->view('docs/redis', $data);
    }

    public function aws_s3(): void
    {
        $this->view('docs/aws_s3');
    }

    public function aws_s3_example(): void
    {
        $s3Manager = new AWS_S3_Model();

        echo "<pre>" . PHP_EOL;

        // Upload File
        // We are uploading the Lightsail logo from the Public image directory.
        $s3Manager->uploadFile('light-sail-logo.png', '../public/img/light-sail-logo.png');
        echo "File uploaded from: app/public/img/light-sail-logo.png" . PHP_EOL . PHP_EOL;

        // List Files
        $files = $s3Manager->listFiles();
        echo "List of Files:" . PHP_EOL;
        print_r($files);
        echo PHP_EOL;

        // Get Timestamp of file
        $timestamp = $s3Manager->getFileTimestamp('light-sail-logo.png');
        echo "Timestamp on the file light-sail-logo.png: " . PHP_EOL;
        echo $timestamp . PHP_EOL . PHP_EOL;

        // Get Timestamp of file
        $filesize = $s3Manager->getFileSize('light-sail-logo.png');
        echo "File Size of light-sail-logo.png: " . PHP_EOL;
        echo $filesize . PHP_EOL . PHP_EOL;

        // Download File
        $s3Manager->downloadFile('light-sail-logo.png', '../public/img/light-sail-logo-2.png');
        echo "File downloaded to: app/public/img/light-sail-logo-2.png " . PHP_EOL;
        echo "Check that directory to see the downloaded file!" . PHP_EOL;
    }

    public function api(): void
    {
        $this->view('docs/api');
    }

    public function geocoder(): void
    {
        $Geocoder = new Geocoder(); // Directly accessible when installed as a core service here: app/init.php

        /*
         * GET MULTIPLE ADDRESSES
         */

        $multiple_address_input = array(
            array(
                'id' => '111',
                'street' => '1600 Amphitheatre Parkway',
                'city' => 'Mountain View',
                'state' => 'CA',
                'zip' => '94043',
            ),
            array(
                'id' => '222',
                'street' => '1600 Pennsylvania Avenue',
                'city' => 'Washington',
                'state' => 'DC',
                'zip' => '20500',
            ),
            // Add more addresses as needed
        );

        $multiple_address_output = $Geocoder->geocodeMultipleAddresses('temp_csv_filename', $multiple_address_input, 'destination.csv');

        /*
         * GET SINGLE ADDRESS
         */

        $single_address_input = "4600 Silver Hill Rd, Washington, DC 20233";

        $single_address_output = $Geocoder->geocodeSingleAddress($single_address_input);

        $data = [
            'multiple_address_input' => $multiple_address_input,
            'multiple_address_output' => $multiple_address_output,
            'single_address_input' => $single_address_input,
            'single_address_output' => $single_address_output,
            ];

        $this->view('docs/geocoder', $data);
    }

    public function rabbitmq(): void
    {
        // A status message displayed on document page.
        $status_messages= '';

        // PRODUCE - Send Message to RabbitMQ
        try {
            // Open Connection
            $rabbit = new RabbitMQ(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_DEFAULT_USER, RABBITMQ_DEFAULT_PASS);

            // Publish a message with the routing key 'user.created'
            $rmq_message = json_encode(['user_id' => 123, 'name' => 'John Doe']);
            $rabbit->publish('user.created', $rmq_message);

            // Close the connection
            $rabbit->close();

            // Append status to the documentation message output.
            $status_messages .= "Produced: " . $rmq_message . PHP_EOL;

        } catch (Exception $e) {
            // Handle exception
            echo 'Error: ' . $e->getMessage();
        }

        // Set delay between Producing and Consuming. Give RabbitMQ time to prepare the queue.
        sleep(5);

        // Consume - Get Messages from RabbitMQ
        try {
            // Open Connection
            $rabbit = new RabbitMQ(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_DEFAULT_USER, RABBITMQ_DEFAULT_PASS);

            // This callback function will process your messages, if any are received.
            $callback = function ($msg) use (&$status_messages) {

                // TODO: You can process the message here
                // $msg->body, for this example, returns a json object with user info.

                // Acknowledge the message
                $msg->ack();

                // Append status to the documentation message output.
                $status_messages .= 'Consumed: ' . $msg->body . PHP_EOL;
                $status_messages .= "Delivery tag: " . $msg->delivery_info['delivery_tag'] . PHP_EOL;
            };

            // Consume messages from the 'user_queue' associated to the 'user.created' routing-key
           $rabbit->consume('user_queue', ['user.created'], $callback, $status_messages);

            // Close the connection
            $rabbit->close();

        } catch (Exception $e) {
            // Handle exception
            echo 'Error: ' . $e->getMessage();
        }

        // Prepare the View (web page)
        $data = [
            'status_messages' => $status_messages
        ];

        $this->view('docs/rabbitmq', $data);
    }

}