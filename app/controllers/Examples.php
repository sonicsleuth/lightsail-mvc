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
        // You may load Models globally when this Controller is called, but it is bert practice to specific
        // Models used only for a specific function within that function, see below.
    }

    public function passing_data(): void
    {
        // Getting Users from the DB Model.
        // $user = $this->model('User');
        // $users = $user->getUsers();

        // Setting some demo data for documentation examples.
        $users = [
          [
              'name' => 'John Smith',
              'email' => 'john@smith.com',
          ],
            [
                'name' => 'Jane White',
                'email' => 'jane@white.com',
            ]
        ];

        $data = [
            'is_admin' => 'yes', // some arbitrary value we may need in the View.
            'users' => $users // Zero or more user records.
        ];

        $this->view('docs/passing_data', $data);
    }

}