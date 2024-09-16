<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * This Controller is used to set up the MySQL database for use with the documentation.
 *
 * You run the default index method of this Controller after building your local Docker Container,
 * or to rest the database as a start-over.
 *
 *  To access the MySQL database container run the following command in your terminal: enter "password" at prompt.
 *  docker exec -it lightsail-mysql-container mysql -u root -p
 */

class Setup extends Controller
{
    private mixed $migrationModel;

    public function __construct()
    {
        $this->load_helper(['debug']);
        $this->load_helper(['view']);

        $this->migrationModel = $this->model('Migration');
    }

    /**
     * @return void
     * Documentation for setting up the database.
     *
     */
    public function index(): void
    {
        $this->view('docs/setup');
    }

    /**
     * @return void
     * Migrate all tables into the database for demo and documentation use.
     */
    public function migrateUp(): void
    {
        // Install Users
        $this->migrationModel->dropUserTable();
        $this->migrationModel->createUserTable();
        $this->migrationModel->populateUserTable();

        // Install Logs
        $this->migrationModel->dropLogsTable();
        $this->migrationModel->createLogsTable();
        $this->migrationModel->populateLogsTable();

        // Install Sessions
        $this->migrationModel->dropSessionsTable();
        $this->migrationModel->createSessionsTable();
        $_SESSION['migration_status'] = "success";
        $_SESSION['mvc_status'] = "success";

        $data = [
            "status" => "success",
        ];

        $this->view('docs/setup', $data);
    }

    public function migrateDown(): void
    {
        $this->migrationModel->dropAllTables();
    }

    /**
     * @return void
     * Run /setup/phpinfo in a browser to view all PHP settings.
     */
    public function phpinfo(): void
    {
        echo phpinfo();
    }

}
