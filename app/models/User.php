<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

class User extends Model
{
    private Model $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Model();
    }

    /**
     * @return array
     * Return all Users
     * See /app/core/Model.php for available methods to interact with the database.
     */
    public function getUsers(): array
    {
        return $this->db->select("users");
    }

}