<?php 

class TaskController {

    /**
     * Add new task
     *
     * @param array $data - data about new task
     * @return array $json_data - information about task.
     */
    public function add($data) {
            
        $task_obj = new Task();
        $task_id = $task_obj->save($data);        
        if(!is_numeric($task_id)) {
            $json_data = array(
                'errors' => $task_obj->validation_errors);
        } else {
            $json_data = array(
                'result' => 1, 
                'task_data' => $task_obj->get_data($task_id));
        }

        return $json_data;
    }

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

        // Sanitize name, content for the user in advance.
        foreach($tasks_data as $key => $task_data) { 
            $tasks_data[$key]['name_clean'] = h($task_data->name);
            $tasks_data[$key]['content_clean'] = h($task_data->content);
        }

        $json_data = array(
            'data' => to_array($tasks_data));

        // Return them.
        return $json_data;
    }
}