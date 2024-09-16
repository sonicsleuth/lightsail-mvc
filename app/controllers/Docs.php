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

    public function phpinfo()
    {
        echo phpinfo();
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

}