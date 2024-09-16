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

}