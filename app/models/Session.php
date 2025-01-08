<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/*
    Database Sessions (Updated for use with PHP v8+)

    Requirements:
    Create the following mysql table in your database if you are
    implementing this Session Model.

    CREATE TABLE sessions (
        session_id CHAR(32) NOT NULL,
        session_data TEXT NOT NULL,
        session_lastaccesstime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (session_id)
    );

    Proceed to set and retrieve values by key from $_SESSION
	$_SESSION['my_key'] = 'some value';
	$my_value = $_SESSION['my_key'];

	To Destroy the session during logout, empty your $_SESSION within a Controller.
	$_SESSION = [];
*/

class Session extends Model implements SessionHandlerInterface
{
    private $db;
    private $session_id;

    public function __construct()
    {
        parent::__construct(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);

        // Instantiate new Database object
        $this->db = new Model(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);

        // Set this class as the session handler
        session_set_save_handler($this, true);

        // Register the shutdown function to handle session writes
        register_shutdown_function('session_write_close');

        // Set a custom session ID if not already set
        if (!isset($_COOKIE["PHPSESSID"])) {
            session_id($this->makeSessionId());
        }

        // Start the session
        session_start([
            'cookie_lifetime' => 86400, // 1 day
        ]);
    }

    /*
    Opening the Session - The first stage the session goes through
    is the opening of the session file. Here you can perform any
    action you like; the PHP documentation indicates that this function
    should be treated as a constructor, so you could use it to initialize
    class variables if you’re using an OOP approach.
    */
    #[ReturnTypeWillChange] function open($savePath, $sessionName): true
    {
        $sql = "INSERT INTO sessions SET session_id = :session_id" .
            ", session_data = '' ON DUPLICATE KEY UPDATE session_lastaccesstime = NOW()";

        $bind = [
            ':session_id' => session_id(),
        ];

        $this->db->run($sql, $bind);

        return true;
    }

    /*
	Closing the session occurs at the end of the session life cycle,
	just after the session data has been written. No parameters are
	passed to this callback so if you need to process something here
	specific to the session, you can call session_id() to obtain the ID.
	*/
    #[ReturnTypeWillChange] function close(): true
    {
        $this->session_id = session_id();
        return true;
    }

    /*
    Immediately after the session is opened, the contents of the session
    are read from whatever store you have nominated and placed into the $_SESSION array.
    It is important to understand that this data is not pulled every time you access a
    session variable. It is only pulled at the beginning of the session life cycle when
    PHP calls the open callback and then the read callback.
    */
    #[ReturnTypeWillChange] public function read($session_id)
    {
        $sql = "SELECT session_data FROM sessions WHERE session_id = :session_id";
        $bind = [':session_id' => $session_id];
        $data = $this->db->run($sql, $bind);

        // Return the session data or an empty string
        return $data[0]['session_data'] ?? '';
    }


    /*
    Writing the data back to whatever store you’re using occurs either at the end of
    the script’s execution or when you call session_write_close().
    */
    #[ReturnTypeWillChange] public function write($session_id, $session_data): true
    {
        $sql = "INSERT INTO sessions (session_id, session_data, session_lastaccesstime) 
                VALUES (:session_id, :session_data, NOW()) 
                ON DUPLICATE KEY UPDATE session_data = :session_data, session_lastaccesstime = NOW()";

        $bind = [
            ':session_id' => $session_id,
            ':session_data' => $session_data,
        ];

        $this->db->run($sql, $bind);

        return true;
    }

    /*
    Destroying the session manually is essential especially when using sessions
    as a way to secure sections of your application. The callback is called when
    the session_destroy() function is called.

    In its default session handling capability, the session_destroy() function will
    clear the $_SESSION array of all data. The documentation on php.net states that
    any global variables or cookies (if they are used) will not cleared, so if you
    are using a custom session handler like this one you can perform these tasks in
    this callback also.
    */
    #[ReturnTypeWillChange] public function destroy($session_id): true
    {
        $sql = "DELETE FROM sessions WHERE session_id = :session_id";
        $bind = [':session_id' => $session_id];
        $this->db->run($sql, $bind);

        // Clear the session cookie
        setcookie(session_name(), '', time() - 3600);

        return true;
    }

    /*
    Garbage Collection - The session handler needs to cater to the fact that the
    programmer won’t always have a chance to manually destroy session data.
    For example, you may destroy session data when a user logs out, and it is no
    longer needed, but there’s no guarantee a user will use the logout functionality
    to trigger the deletion. The garbage collection callback will occasionally be
    invoked by PHP to clean out stale session data. The parameter that is passed
    here is the max lifetime of the session which is an integer detailing the
    number of seconds that the lifetime spans.
    */
    #[ReturnTypeWillChange] public function gc($lifetime): true
    {
        $sql = "DELETE FROM sessions WHERE session_lastaccesstime < DATE_SUB(NOW(), INTERVAL :lifetime SECOND)";
        $bind = [':lifetime' => $lifetime];
        $this->db->run($sql, $bind);

        return true;
    }

    /*
    Generate a genesis Session ID.
    Called by session->open if there is no session cookie found.
    */
    /**
     * @throws \Random\RandomException
     */
    private function makeSessionId(): string
    {
        return bin2hex(random_bytes(16)); // Generates a secure 32-character session ID
    }

    /*
	Simple debugger for dumping all the Session data from the database.
	*/
    public function getSessionData()
    {
        return $this->db->select('sessions');
    }

}