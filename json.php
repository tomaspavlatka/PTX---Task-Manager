<?php 
header('content-type: application/json; charset=utf-8');

require realpath(__DIR__). '/Config/bootstrap.php';
require CONTROLLER . 'TaskController.php';
require MODEL . 'Task.php';
require MODEL_JSON . 'TaskJson.php';

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
        case 'task':
            $controller = new TaskController();
            $data = $controller->task($data);
            break;
        case 'task_add':
            $controller = new TaskController();
            $data = $controller->add($data);
            break;
        case 'task_add_time':
            $controller = new TaskController();
            $data = $controller->add_time($data);
            break;
        case 'task_close':
            $controller = new TaskController();
            $data = $controller->close($data);
            break;
        case 'task_open':
            $controller = new TaskController();
            $data = $controller->open($data);
            break;
        case 'tasks':
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