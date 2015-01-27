<?php
define('APP', realpath(__DIR__ . '/../'));
define('DS', '/');
require APP . DS . 'Config' . DS . 'bootstrap.php';
require CONTROLLER . 'TaskController.php';

abstract class AppTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();     
    }

    /**
     * Tear Down
     *  
     * clean after all tests are done.
     */
    public function tearDown() {
        $this->_drop_tasks();
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

    /**
     * Drop tasks.
     *
     * drops tasks for each test case.
     */
    protected function _drop_tasks() {}
}