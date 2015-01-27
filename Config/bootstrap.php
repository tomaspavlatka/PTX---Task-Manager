<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

date_default_timezone_set('Europe/Athens');

// Constants.
define('TPL', APP . DS . 'tpls' . DS);
define('CONTROLLER', APP . DS . 'Controller' . DS);
define('MODEL', APP . DS . 'Model' . DS);
define('MODEL_JSON', APP . DS . 'ModelJson' . DS);
define('ITEMS_PER_PAGE', 10);
define('STORAGE_TYPE', 'mysql'); // another option is json
define('STORAGE_TASK', APP . DS . '_storage' . DS . 'tasks' . DS) ; // where json files are stored.

define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tomas_tasks');
define('DB_CHARSET', 'utf8');

// Include autoload.
require APP . DS . 'vendor' . DS . 'autoload.php';

/**
 * Simple function for print_r. Function will call print_r for $data
 *
 * @param string|array $data Data to be debugged.
 */
function debug($data) {
    echo '<pre>';
        print_r($data);
    echo '</pre>';
}

/**
 * Convenience method for htmlspecialchars.
 */
function h($text, $flag = ENT_QUOTES, $encoding = 'UTF-8') {
    if(is_array($text)) {
        $texts = array();
        foreach($text as $key => $value) {
            $texts[$key] = h($value, $flag, $encoding);
        }
        return $texts;
    } elseif(is_bool($text)) {
        return $text;
    }

    return htmlspecialchars($text, $flag, $encoding);
}

function to_array($data) {

    $array_data = array();
    if(!empty($data)) {
        foreach($data as $key => $value) {
            if(is_object($value)) {
                foreach ($value as $v_key => $v_value) {
                    if(is_object($v_value)) {
                        $array_data[$key][$v_key] = to_array($v_value);
                    } else {
                        $array_data[$key][$v_key] = $v_value;
                    }
                }
            } else {
                $array_data[$key] = $value;
            }
        }
    }

    return $array_data;    
}