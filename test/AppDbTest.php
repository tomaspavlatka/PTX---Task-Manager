<?php 
require_once realpath(__DIR__ . '/../') . '/Config/bootstrap.php';
require_once CONTROLLER . 'TaskController.php';

abstract class AppDbTest extends PHPUnit_Extensions_Database_TestCase {
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection() {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                $dsn = sprintf('mysql:dbname=%s;host=localhost', DB_NAME);
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, DB_NAME);
        }

        return $this->conn;
    }

    /**
     * Compare Arrays contains
     *
     * simple function which compares 2 arrays whether $expected in part of $returned
     * @param array $expected - array which was expected
     * @param array $return - array which was returned
     * @return boolean
     */
    protected function _compare_arrays_contains($expected, $returned) {
        foreach($expected as $key => $value) {
            if(is_array($value)) {
                if(array_key_exists($key, $returned)) {
                    return $this->_compare_arrays_contains($expected[$key], $returned[$key]);
                } else {
                    return false;
                }
            } else {
                if(!array_key_exists($key, $returned)) {
                    return false;
                } else if($value != $returned[$key]) {
                    return false;
                }
            }
        }

        return true;
    }
}