<?php
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');

/*
 * ConMan - MySQL Connection Manager with persistent connection pooling
 */

class ConMan
{   
    private static $pdoCore = null;
    private static $pdoApp = null;

    // Get (or create) the persistent PDO connection for core 
    public static function getCore(): PDO
    {
        $newCon = false;

        if (self::$pdoCore === null) 
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
                self::$pdoCore = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
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
            ConMan::reset(self::$pdoCore);
        }
        return self::$pdoCore;
    }

    // Get (or create) the persistent PDO connection for application 
    public static function getApp(string $host, int $port, string $database, string $username, string $password): PDO
    {
        $newCon = false;

        if (self::$pdoApp === null) 
        {
            // Indicate creating enw connection
            $newCon = true;

            // Create dsn
            $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", $host, $port, $database);

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
                self::$pdoApp = new PDO($dsn, $username, $password, $options);
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
            ConMan::reset(self::$pdoApp);
        }
        return self::$pdoApp;
    }

    /*
     * Optional: Reset session state (good practice with persistent connections)
     * Call this after ConMan::getCore()
     * Example: ConMan::getCore()
     *          ConMan::reset()
     */
    public static function reset(PDO $pdo): void
    {
        if (self::$pdoCore) 
        {
            self::$pdoCore->query('ROLLBACK; SET autocommit=1;');
            // Clear temporary tables, user variables, etc. here if needed later on
        }

        if (self::$pdoApp) 
        {
            self::$pdoApp->query('ROLLBACK; SET autocommit=1;');
            // Clear temporary tables, user variables, etc. here if needed later on
        }
    }

    // Prevent cloning & direct instantiation
    private function __construct() {} //need to check if ConMan::get() needs to be created here
    private function __clone() {}
}

?>