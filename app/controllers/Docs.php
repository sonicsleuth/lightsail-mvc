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
        $redis = new RedisModel();

        // Store some simple values:
        $redis->set('name', 'John Doe');
        $redis->set('date', date("Y-m-d H:i:s"));

        $name = $redis->get('name');
        $date = $redis->get('date');

        // Store a JSON object:
        // Create a sample JSON object (as an associative array in PHP)
        $data = [
            "name" => "John Doe",
            "email" => "john@example.com",
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
        $this->model('AWSS3Model');
        $s3Manager = new AWSS3Model();

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

}