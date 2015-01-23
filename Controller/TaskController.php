<?php 

class TaskController {

    /**
    * Listing existing tasks.
    */
    public function index($data) {        

        // Get Data.
        $task_obj = new Task();
        $params = array(
            'order' => array('name' => 'ASC'),
            'conditions' => array(
                'status' => 1));
        $tasks_data = $task_obj->find_all($params);

        $json_data = array(
            'data' => $tasks_data);

        // Return them.
        return $json_data;
    }
}