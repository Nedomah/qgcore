<?php
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');

/*
 * ConMan - MySQL Connection Manager with persistent connection pooling
 */

class ConMan
{   
    private static $pdo = null;

    // Get (or create) the persistent PDO connection
    public static function get(): PDO
    {
        $newCon = false;

        if (self::$pdo === null) 
        {
            // Indicate creating enw connection
            $newCon = true;

            // Create dsn
            $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", $_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_NAME']);

            // Set options
            $options =  [
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // always throw on error
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // nice default
                            PDO::ATTR_PERSISTENT         => true,                     // ← THIS IS THE POOLING MAGIC
                            PDO::ATTR_EMULATE_PREPARES   => false,                    // real prepared statements
                            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4, sql_mode="TRADITIONAL"', // optional cleanup
                        ];

            // Try to open connection
            try 
            {
                self::$pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
            } 
            catch (PDOException $e) 
            {
                // Display in developement
                if  ($_ENV['APP_DEBUG'] === true && $_ENV['APP_ENV'] === "development") 
                {
                    throw new Exception('MySQL connection manager failed: ' . $e->getMessage());
                }
                // Log in production, don't expose details
                else 
                {
                    error_log('MySQL connection manager failed: ' . $e->getMessage());    
                    throw new Exception('Database connection unavailable');
                }
            }
        }
         
        // Ensure existing connections are always reset before passing it back 
        if ($newCon === false)    
        {  
            ConMan::reset();
        }
        return self::$pdo;
    }

    /*
     * Optional: Reset session state (good practice with persistent connections)
     * Call this after ConMan::get()
     * Example: ConMan::get()
     *          ConMan::reset()
     */
    public static function reset(): void
    {
        if (self::$pdo) 
        {
            self::$pdo->query('ROLLBACK; SET autocommit=1;');
            // Clear temporary tables, user variables, etc. here if needed later on
        }
    }

    // Prevent cloning & direct instantiation
    private function __construct() {} //need to check if ConMan::get() needs to be created here
    private function __clone() {}
}

?>