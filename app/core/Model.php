<?php
/**
 * A PDO Abstraction for common CRUD statements with auto-column binding.
 * For use with MySQL for example.
 */

class Model extends PDO {

    private string $error;
    private $sql;
    private $bind;
    private $errorCallbackFunction;
    private $errorMsgFormat;
    private $errorCssPath = 'model_error.css'; // Styles to pretty up the custom error output.

    public function __construct()
    {
        $dsn = sprintf('%s:host=%s;dbname=%s', DB_DRIVER, DB_HOSTNAME, DB_DATABASE);
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        ];

        try {
            parent::__construct($dsn, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log('DB Model Connection Error: ' . $this->error, 0);

            // Consider using a more user-friendly message in production
            if (ini_get('display_errors')) {
                echo 'DB Connection Error: ' . $this->error;
            } else {
                echo 'A database connection error occurred.';
            }
        }
    }

    /**
     * @param string $sql
     * @param array $bind
     * @param bool $entity_decode
     * @return mixed
     *  This method is used to run free-form SQL statements that can't be handled by the included delete, insert, select,
     *  or update methods. If no SQL errors are produced, this method will return the number of affected rows for
     *  DELETE, INSERT, and UPDATE statements, or an object of results for SELECT, DESCRIBE, and PRAGMA statements.
     *
     * HTML Entities returned from 'select' queries will be decoded by default. Set $entity_decode = false otherwise.
     */
    public function run(string $sql, array $bind = [], bool $entity_decode = true): mixed
    {
        $this->sql   = trim($sql);
        $this->bind  = $this->cleanup($bind);
        $this->error = "";

        try {
            $pdostmt = $this->prepare($this->sql);
            if ($pdostmt->execute($this->bind) !== false) {
                $sqlType = strtolower(strtok($this->sql, ' '));

                switch ($sqlType) {
                    case 'with':
                    case 'select':
                    case 'describe':
                    case 'pragma':
                        $results = $pdostmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($entity_decode) {
                            array_walk_recursive($results, fn(&$item) => $item = htmlspecialchars_decode($item));
                        }
                        return $results;

                    case 'delete':
                    case 'update':
                        return $pdostmt->rowCount();

                    case 'insert':
                        return $this->lastInsertId();
                }
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->debug();
            return false;
        }

        return false;
    }

    /**
     * @param string $table
     * @param string $where
     * @param array $bind
     * @param string $fields
     * @param bool $entity_decode
     * @return mixed
     *
     *  Example #1 - Return All.
     *  $results = $this->db->select("table_name");
     *
     *  Example #2 - Where condition.
     *  $results = $this->db->select("table_name", "Gender = 'male'");
     *
     *  Example #3 - Prepared statement.
     *  $search = "John"; - String to find.
     *
     *  Where clause variable bindings:
     *  $bind = array(
     *       ":search" => "%$search"
     *  );
     *
     *  $results = $this->db->select("table_name", "FName LIKE :search", $bind);
     *
     *  One or more records are returned as array of array. If no records found, FALSE is returned.
     *  HTML Entities returned from the database will be decoded by default. Set $entity_decode = false otherwise.
     */
    public function select(string $table, string $where = "", array $bind = [], string $fields = "*", bool $entity_decode = true): mixed
    {
        // Build the SQL query
        $sql = sprintf('SELECT %s FROM %s', $fields, $table);

        if (!empty($where)) {
            $sql .= " WHERE $where";
        }

        $sql .= ";";

        // Execute the query and retrieve the data
        $data = $this->run($sql, $bind, $entity_decode);

        // Return the data or false if empty
        return !empty($data) ? $data : false;
    }


    /**
     * @param string $table
     * @param string $where
     * @param array $bind
     * @param string $fields
     * @param bool $entity_decode
     * @return mixed
     *
     *  Use this method in place of select() when you want to return a single record.
     *
     *  This method functions identically as select() accept it returns the results as a single array
     *  and will LIMIT the results to the first record found.
     */
    public function selectOne(string $table, string $where = "", array $bind = [], string $fields = "*", bool $entity_decode = true): mixed
    {
        // Build the SQL query with LIMIT 1
        $sql = sprintf('SELECT %s FROM %s', $fields, $table);

        if (!empty($where)) {
            $sql .= " WHERE $where LIMIT 1";
        }

        $sql .= ";";

        // Execute the query and retrieve the data
        $data = $this->run($sql, $bind, $entity_decode);

        // Return the first result or false if no data is found
        return $data[0] ?? false;
    }


    /**
     * @param string $sql
     * @param array $bind
     * @param bool $entity_decode
     * @return mixed
     *
     *  Use this method when complex SQL statements are required, like table JOINS, are required.
     *
     *  Example:
     *  $sql = "select * from users u join preferences p on p.user_id = u.id where p.role = :role ";
     *  $bind = array (':role' => 'admin');
     *  $results = $this->db->selectExtended($sql, $bind);
     *
     *  HTML Entities returned from the database will be decoded by default. Set $entity_decode = false otherwise.
     */
    public function selectExtended(string $sql, array $bind = [], bool $entity_decode = true): mixed
    {
        $data = $this->run($sql, $bind, $entity_decode);

        if (empty($data)) {
            return false;
        }

        // Determine if the result is a single column or multiple columns
        return (count($data[0]) > 1) ? $data : $data[0];
    }


    /**
     * @param string $table
     * @param array $info
     * @return mixed
     *
     *  If no SQL errors are produced, this method will return the number of rows affected by the INSERT statement.
     *
     *  Column Names and Values to Insert:
     *  $insert = array(
     *       "FName" => "John",
     *       "LName" => "Doe",
     *       "Age" => 26,
     *       "Gender" => "male"
     *  );
     *  $this->db->insert("table_name", $insert);
     */
    public function insert(string $table, array $info): mixed
    {
        $fields = $this->filter($table, $info);

        $columns = implode(', ', $fields);
        $placeholders = implode(', :', $fields);
        $sql = sprintf("INSERT INTO %s (%s) VALUES (:%s);", $table, $columns, $placeholders);

        $bind = array_map(fn($field) => $info[$field], $fields);
        $bind = array_combine(array_map(fn($field) => ":$field", $fields), $bind);

        return $this->run($sql, $bind);
    }


    /**
     * @param string $table
     * @param array $info
     * @param string $where
     * @param array $bind
     * @return mixed
     *
     *  If no SQL errors are produced, this method will return the number of rows affected by the UPDATE statement.
     *
     * Column Name and Value to update:
     *  $update = array(
     *       "Age" => 24
     *  );
     *
     *  Where clause variable bindings:
     *  $bind = array(
     *       ":fname" => "Jane",
     *       ":lname" => "Doe"
     *  );
     *
     *  $this->db->update("table_name", $update, "FName = :fname AND LName = :lname", $bind);
     */
    public function update(string $table, array $info, string $where, array $bind = []): mixed
    {
        $fields = $this->filter($table, $info);

        $setClause = implode(', ', array_map(fn($field) => "$field = :update_$field", $fields));
        $sql = sprintf("UPDATE %s SET %s WHERE %s;", $table, $setClause, $where);

        // Merge the existing bind parameters with the new ones for the update
        $updateBind = array_combine(
            array_map(fn($field) => ":update_$field", $fields),
            array_map(fn($field) => $info[$field], $fields)
        );

        $bind = array_merge($this->cleanup($bind), $updateBind);

        return $this->run($sql, $bind);
    }


    /**
     * @param string $table
     * @param string $where
     * @param array $bind
     * @return bool
     *
     *  If no SQL errors are produced, this method will return the number of rows affected by the DELETE statement.
     *
     *  Method #1
     *  $this->db->delete("table_name", "Age < 30");
     *
     *  Method #2 w/Prepared Statement
     *
     *  Where clause variable bindings:
     *  $bind = array(
     *       ":lname" => "Smith"
     *  )
     *
     *  $this->db->delete("table_name", "LName = :lname", $bind);
     */
    public function delete(string $table, string $where, array $bind = []): bool
    {
        $sql = sprintf("DELETE FROM %s WHERE %s;", $table, $where);
        $bind = $this->cleanup($bind);

        return $this->run($sql, $bind) !== false;
    }

    /**
     * @param string $table
     * @param array $info
     * @return array
     *
     *  Automated table binding for MySql or SQLite.
     */
    private function filter(string $table, array $info): array
    {
        $driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
        $key = '';
        $sql = '';

        switch ($driver) {
            case 'sqlite':
                $sql = "PRAGMA table_info('$table');";
                $key = 'name';
                break;
            case 'mysql':
            case 'mysqli':
                $sql = "DESCRIBE $table;";
                $key = 'Field';
                break;
            default:
                $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '$table';";
                $key = 'column_name';
                break;
        }

        $list = $this->run($sql);
        if ($list !== false) {
            $fields = array_column($list, $key);
            return array_values(array_intersect($fields, array_keys($info)));
        }

        return [];
    }


    /**
     * @param mixed $bind
     * @return array
     *
     *  Ensure we have an array to work with.
     */
    private function cleanup(mixed $bind): array
    {
        if (is_array($bind)) {
            return $bind;
        }

        return !empty($bind) ? [$bind] : [];
    }


    /**
     * setErrorCallbackFunction()
     * 
     * The error message can then be displayed, emailed, etc within the callback function.
     *
     * Example:
     *
     * function myErrorHandler($error) {
     * }
     *
     * $db = new db("mysql:host=127.0.0.1;port=0000;dbname=mydb", "dbuser", "dbpasswd");
     * $this->db->setErrorCallbackFunction("myErrorHandler");
     *
     * Text Version
     * $this->db->setErrorCallbackFunction("myErrorHandler", "text");
     *
     * Internal/Built-In PHP Function
     * $this->db->setErrorCallbackFunction("echo");
     *
     * @param $errorCallbackFunction
     * @param string $errorMsgFormat
     */
    public function setErrorCallbackFunction(callable|string $errorCallbackFunction, string $errorMsgFormat = "html"): void
    {
        $errorCallbackFunction = strtolower($errorCallbackFunction);

        if (in_array($errorCallbackFunction, ["echo", "print"])) {
            $errorCallbackFunction = "print_r";
        }

        if (is_callable($errorCallbackFunction) || method_exists($this, $errorCallbackFunction)) {
            $this->errorCallbackFunction = $errorCallbackFunction;

            $this->errorMsgFormat = in_array(strtolower($errorMsgFormat), ["html", "text"]) ? strtolower($errorMsgFormat) : "html";
        }
    }


    /**
     * @return void
     *
     *  A better PDO debugger, just because.
     */
    private function debug(): void
    {
        // If no other error handler is defined, then use this.
        if (empty($this->errorCallbackFunction)) {
            return;
        }

        $error = [
            "Error" => $this->error,
            "SQL Statement" => $this->sql ?? null,
            "Bind Parameters" => !empty($this->bind) ? trim(print_r($this->bind, true)) : null,
        ];

        // Capture the first relevant backtrace entry
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $info) {
            if ($info["file"] !== __FILE__) {
                $error["Backtrace"] = sprintf('%s at line %d', $info["file"], $info["line"]);
                break;
            }
        }

        // Prepare the error message
        $msg = $this->prepareErrorMessage($error);

        // Call the error callback function
        $callback = $this->errorCallbackFunction;
        $this->{$callback}($msg);
    }

    /**
     * @param array $error
     * @return string
     */
    private function prepareErrorMessage(array $error): string
    {
        $msg = "";

        if ($this->errorMsgFormat === "html") {
            $error["Bind Parameters"] = !empty($error["Bind Parameters"]) ? "<pre>{$error["Bind Parameters"]}</pre>" : null;

            $cssPath = dirname(__FILE__) . $this->errorCssPath;
            if (file_exists($cssPath)) {
                $css = trim(file_get_contents($cssPath));
                $msg .= "<style type=\"text/css\">\n$css\n</style>";
            }

            $msg .= '<div class="db-error"><h3>SQL Error</h3>';
            foreach ($error as $key => $val) {
                if ($val !== null) {
                    $msg .= "<label>$key:</label> $val";
                }
            }
            $msg .= '</div>';
        } elseif ($this->errorMsgFormat === "text") {
            $msg .= "SQL Error\n" . str_repeat("-", 50);
            foreach ($error as $key => $val) {
                if ($val !== null) {
                    $msg .= "\n\n$key:\n$val";
                }
            }
        }

        return $msg;
    }


    /**
     * @param $msg
     * @return void
     *
     * Example Callback Function.
     */
    public function basicCallbackFunction($msg) {
        print_r($msg);
    }
}