<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/15/2015
 * Time: 7:58 AM
 */
namespace TestingCenter\Utilities;

class DatabaseConnection
{

    private static $instance = null;
    private static $host = "localhost";
    private static $dbname = "testingcenter_dev"; //YOUR W#, NOT THIS <--
    private static $user = "testingcenterdev";
    private static $pass = "WeberCS!";

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!static::$instance === null) {
            return static::$instance;
        } else {
            try {
                $connectionString = "mysql:host=" . static::$host . ";dbname=" . static::$dbname;
                static::$instance = new \PDO($connectionString, static::$user, static::$pass);
                static::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
                return static::$instance;
            } catch (PDOException $e) {
                echo "Unable to connect to the database: " . $e->getMessage();
                die();
            }
        }
    }
}
