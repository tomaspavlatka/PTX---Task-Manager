<?php 
header('content-type: application/json; charset=utf-8');

define('APP', __DIR__);
define('DS', '/');
require APP . DS . 'Config' . DS . 'bootstrap.php';

$action = null;
$request_uri = str_replace('/json/', null, $_SERVER['REQUEST_URI']);
if(!empty($request_uri)) {
    $action = str_replace('/', null, $request_uri);
    
    $position = strpos($action, '?');
    if(!empty($position)) {
        $action = substr($action, 0, $position);
    }
}

$data = array();
if(isset($_POST) && !empty($_POST) && array_key_exists('data', $_POST)) {
    $data = @json_decode($_POST['data'], true);
}

if($data === null) {
    $data = array(
        'errors' => array(
            'msg' => 'Data for function are not structured properly.'));
} else {
  
    switch($action) {
        case 'task_add':
            require CONTROLLER . 'TaskController.php';
            require MODEL . 'Task.php';
            $controller = new TaskController();
            $data = $controller->add($data);
            break;
        case 'tasks':
            require CONTROLLER . 'TaskController.php';
            require MODEL . 'Task.php';
            $controller = new TaskController();
            $data = $controller->index($data);
            break;
        default:
            $data = array(
                'errors' => array(
                    'msg' => 'Action is not specified or is not supported.'));
            break;
    }
}

// Response.
echo json_encode($data);