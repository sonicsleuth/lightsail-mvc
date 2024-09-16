<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * This Controller is a collection of example code. 
 * 
 * Some of these example wil require the setup of a database, 
 * but generally should be used as a reference point for best practice.
 */

class Examples extends Controller
{
    public function __construct()
    {
        $this->load_helper(['view']);

        // $user = $this->model('User');
        // You may load Models globally, as shown above, when a Controller is loaded,
        // or load them within the function they are required, see below. The latter has better performance.
    }

    public function passing_data(): void
    {
        $user = $this->model('User');

        $users = $user->getUsers();

        $data = [
            'is_admin' => 'yes', // some arbitrary value we may need in the View.
            'users' => $users // Zero or more user records for the database.
        ];

        $this->view('docs/passing_data', $data);
    }

}