<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/*
 * LOG MODEL
 * This model allows you to store your script logs in a MySQL database (the default model).
 * This model will write those logs locally (on your computer) when the ENVIRONMENT=localhost in the .env file.
 *
 * This model requires the following table set up in your MySQL database:
 * CREATE TABLE logs
 * (
 *     event_type     varchar(45)                        NULL,
 *     event_message  varchar(255)                       NULL,
 *     event_script   varchar(255)                       NULL,
 *     event_datetime datetime DEFAULT CURRENT_TIMESTAMP NULL
 * )
 */

class Log extends Model
{
    private Model $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Model();
    }

    /**
     * @return array|bool
     * Get all log records.
     */
    public function getLogs(): array|bool
    {
        return $this->db->select("logs");
    }

    /**
     * @param string $event_type
     * @param string $event_script
     * @param string $event_message
     * @return bool
     * Add a Log Message to the database.
     *
     *  How to use within a Controller:
     *  $event_type = 'error'; - this can be any custom classification, like error, success, warning, etc.
     *  $event_script = __DIR__.$_SERVER['SCRIPT_NAME'];
     *  $event_message = 'Test::log example log entry'; - A good format is... ClassName::MethodName::message
     *  $this->log->addRecord($event_type, $event_script, $event_message);
     */
    public function addMessage(string $event_type = "unspecified", string $event_script = "unspecified", string $event_message = ""): bool
    {
        // Use trimming and lowering in one step with strtr for better performance
        $event_type = strtolower(trim($event_type));
        $event_script = strtolower(trim($event_script));
        $event_message = trim($event_message);

        $data = [
            'event_type' => $event_type,
            'event_script' => $event_script,
            'event_message' => $event_message
        ];

        if (!$this->db->insert('logs', $data)) {
            return false;
        }

        // Write message to a local log file if running on localhost.
        if (strtolower(ENVIRONMENT) === 'localhost') {
            $this->writeMessageToLocalLogFile($event_type, $event_script, $event_message);
        }

        return true;
    }


    /**
     * @param string $event_type
     * @param string $event_script
     * @param string $event_message
     * @return void
     * Write a message to a local file on your filesystem when the ENVIRONMENT=localhost in the .env file.
     */
    public function writeMessageToLocalLogFile(string $event_type = 'unspecified', string $event_script = 'unspecified', string $event_message = ''): bool
    {
        // Clean up input data
        $event_type = strtolower(trim($event_type));
        $event_script = strtolower(trim($event_script));
        $event_message = trim($event_message);

        // Check if the environment is development
        if (strtolower($_ENV['ENVIRONMENT'] ?? '') === 'localhost') {

            // Define log directory and check for existence
            $log_directory = '/srv/app/logs/';
            if (!is_dir($log_directory)) {
                if (!mkdir($log_directory, 0777, true) && !is_dir($log_directory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $log_directory));
                }
            }

            // Build the log file path
            $log_file = $log_directory . $event_type . '.log';

            // Format the log record with timestamp and event details
            $record = sprintf(
                "\r\n%s\t%s\t%s\t%s",
                date('Y/m/d h:i:s'),
                $event_type,
                $event_script,
                $event_message
            );

            // Write log to file, appending it with locking mechanism
            if (file_put_contents($log_file, $record, FILE_APPEND | LOCK_EX) === false) {
                throw new \RuntimeException(sprintf('Unable to write to log file "%s"', $log_file));
            }

            return true;
        }

        return false;
    }
}
