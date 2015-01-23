<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

date_default_timezone_set('Europe/Athens');

// Constants.
define('TPL', APP . DS . 'tpls' . DS);
define('CONTROLLER', APP . DS . 'Controller' . DS);
define('MODEL', APP . DS . 'Model' . DS);

define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_USER', 'tomas_user756');
define('DB_PASS', '!gpIg5PTko0F');
define('DB_NAME', 'tomas_tasks953');
define('DB_CHARSET', 'utf8');

// Include autoload.
require APP . DS . 'vendor' . DS . 'autoload.php';

// Connect to db.
dibi::connect(array(
    'driver'   => DB_DRIVER,
    'host'     => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PASS,
    'database' => DB_NAME,
    'charset'  => DB_CHARSET));

function debug($data) {
    echo '<pre>';
        print_r($data);
    echo '</pre>';
}
