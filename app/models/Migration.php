<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * The Migrations model contains all the methods for setting up a new MySQL database for use with this MVC package.
 *
 * To access the MySQL database container run the following command in your terminal: enter "password" at prompt.
 * docker exec -it lightsail-mysql-container mysql -u root -p
 */

class Migration extends Model
{
    private Model $db;

    public function __construct()
    {
        parent::__construct(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
        $this->db = new Model(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    }

    /* Users table ********************************************************/
    public function createUserTable(): mixed
    {
        $sql = [
            "CREATE TABLE IF NOT EXISTS users (",
            "id INT AUTO_INCREMENT PRIMARY KEY,",
            "username VARCHAR(255) NOT NULL,",
            "password VARCHAR(255) NOT NULL,",
            "role VARCHAR(50) NOT NULL",
            ");"
        ];

        $sql_statement = implode(" ", $sql);

        return $this->db->run($sql_statement);
    }

    public function populateUserTable(): mixed
    {
        $sql = [
            "INSERT INTO users (username, password, role)",
            "VALUES",
            "('john_doe', 'password123', 'admin'),",
            "('jane_smith', 'pass456', 'user'),",
            "('bob_jones', 'qwerty789', 'user');"
        ];

        $sql_statement = implode(" ", $sql);

        return $this->db->run($sql_statement);
    }

    public function dropUserTable(): mixed
    {
        return $this->db->run("DROP TABLE users");
    }

    /* Logs table ********************************************************/

    public function createLogsTable(): mixed
    {
        $sql = [
            "CREATE TABLE IF NOT EXISTS logs (",
            "event_type     varchar(45) NULL,",
            "event_message  varchar(255) NULL,",
            "event_script   varchar(255) NULL,",
            "event_datetime datetime DEFAULT CURRENT_TIMESTAMP NULL",
            ");"
        ];

        $sql_statement = implode(" ", $sql);

        return $this->db->run($sql_statement);
    }

    public function populateLogsTable(): mixed
    {
        $sql = [
            "INSERT INTO logs (event_type, event_message, event_script )",
            "VALUES",
            "('error', 'MyController.php', 'MyController::get - Failed to get records.'),",
            "('success', 'MyController.php', 'MyController::set - Successful operation.'),",
            "('warning', 'MyController.php', 'MyController::update - Process took 2000ms.');"
        ];

        $sql_statement = implode(" ", $sql);

        return $this->db->run($sql_statement);
    }

    public function dropLogsTable(): mixed
    {
        return $this->db->run("DROP TABLE logs");
    }

    /* Sessions table ********************************************************/

    public function createSessionsTable(): mixed
    {
        $sql = [
            "CREATE TABLE IF NOT EXISTS sessions (",
            "session_id CHAR(32) NOT NULL,",
            "session_data TEXT NOT NULL,",
            "session_lastaccesstime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,",
            "PRIMARY KEY (session_id)",
            ");"
        ];

        $sql_statement = implode(" ", $sql);

        return $this->db->run($sql_statement);
    }

    public function dropSessionsTable(): mixed
    {
        return $this->db->run("DROP TABLE sessions");
    }

    /* Clear Database ********************************************************/

    public function dropAllTables(): mixed
    {
        $this->db->run("DROP TABLE users");
        $this->db->run("DROP TABLE logs");
        $this->db->run("DROP TABLE sessions");
    }

}