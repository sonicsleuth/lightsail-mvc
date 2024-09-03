<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

class Home extends Controller
{
    public function __construct()
    {
        $this->load_helper(['view']);
    }

    public function index(string $param1 = '', string $param2 = ''): void
    {
        $this->view('home');
    }

    public function phpinfo(): void
    {
        echo phpinfo();
    }

}