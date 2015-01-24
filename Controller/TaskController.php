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
                'task_data' => $task_obj->get_data($task_id));
        }

        return $json_data;
    }

    /**
     * Add new time report
     *
     * @param array $data - data about new time report
     * @return array $json_data - information about task.
     */
    public function add_time($data) {
            
        if($this->_check_fields($data, array('id', 'time_spent'))) {
            if(!empty($data['id']) && !empty($data['time_spent'])) {
                $task_obj = new Task();
                if($task_obj->add_time($data['id'], $data['time_spent'])) {
                    $json_data = array(
                        'task-data' => $task_obj->validation_errors);    
                } else {
                    $json_data = array(
                        'errors' => $task_obj->validation_errors);    
                }
            } else {
                $json_data = array(
                    'errors' => array(
                        'general' => 'Wrong or missing data.'));
            }
        } else {
            $json_data = array(
                'errors' => array(
                    'general' => 'Wrong or missing data.'));
        }

        return $json_data;
    }

    /**
     * Closes task
     *
     * array $data - data, where key id is key for the close
     * @return array $data - data about the task
     */
    public function close($data) {

        if($this->_check_fields($data, array('id'))) {
            if(!empty($data['id'])) {
                $task_obj = new Task();
                if($task_obj->close($data['id'])) {
                    $json_data = array(
                        'task-data' => $task_obj->validation_errors);    
                } else {
                    $json_data = array(
                        'errors' => $task_obj->validation_errors);    
                }
            } else {
                $json_data = array(
                    'errors' => array(
                        'general' => 'Wrong or missing data.'));
            }
        } else {
            $json_data = array(
                'errors' => array(
                    'general' => 'Wrong or missing data.'));
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
            'page' => 1,
            'order' => array('id' => 'ASC'),
            'conditions' => array(
                'status' => 1));
        if(!empty($data) && is_array($data)) {
            if(array_key_exists('page', $data) && is_numeric($data['page']) && $data['page'] > 1) {
                $params['page'] = (int)$data['page'];
            }
        }
        $tasks_data = $task_obj->find_all($params);

        // Sanitize name, content for the user in advance.
        foreach($tasks_data as $key => $task_data) { 
            $tasks_data[$key]['name_clean'] = h($task_data->name);
            $tasks_data[$key]['content_clean'] = h($task_data->content);
        }

        $data_params = $params;
        $data_params['fields'] = 'id';
        unset($data_params['page']);        
        $data = $task_obj->find_all($data_params);

        $pages_data = array(
            'records' => count($data),
            'active' => $params['page'],
            'has_previous' => $params['page'] > 1 ? true : false,
            'pages' => ceil(count($data) / ITEMS_PER_PAGE));
        $pages_data['has_next'] = $params['page'] < $pages_data['pages'];

        $json_data = array(
            'paginator' => $pages_data,
            'data' => to_array($tasks_data));

        // Return them.
        return $json_data;
    }

    /**
     * Opens closed task
     *
     * @param array $data - data, where key id is key for the close
     * @return array $data - data about the task
     */
    public function open($data) {
        if($this->_check_fields($data, array('id'))) {
            if(!empty($data['id'])) {
                $task_obj = new Task();
                if($task_obj->open($data['id'])) {
                    $json_data = array(
                        'task-data' => $task_obj->validation_errors);    
                } else {
                    $json_data = array(
                        'errors' => $task_obj->validation_errors);    
                }
            } else {
                $json_data = array(
                    'errors' => array(
                        'general' => 'Wrong or missing data.'));
            }
        } else {
            $json_data = array(
                'errors' => array(
                    'general' => 'Wrong or missing data.'));
        }

        return $json_data;
    }

    /**
     * Returns data about specific task
     *
     * @param array $data - data, where key id is key for the close
     * @return array $data - data about the task
     */
    public function task($data) {
        if($this->_check_fields($data, array('id'))) {
            if(!empty($data['id'])) {
                $task_obj = new Task();
                $task_data = $task_obj->get_data($data['id']);

                if(!empty($tasks_data)) {
                     $json_data = array(
                        'task-data' => $task_obj->validation_errors);    
                } else {
                    $json_data = array(
                        'errors' => array(
                            'general' => 'Unknown Task'));    
                }
            } else {
                $json_data = array(
                    'errors' => array(
                        'general' => 'Wrong or missing data.'));
            }
        } else {
            $json_data = array(
                'errors' => array(
                    'general' => 'Wrong or missing data.'));
        }


        return $json_data;
    }

    public function _check_fields($data, $fields) {
        $correct = true;

        foreach($fields as $field) {
            if(!array_key_exists($field, $data)) {
                $correct = false;
                break;
            }
        }

        return $correct;
    }
}