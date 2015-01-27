<?php
require_once MODEL_JSON . 'AppModelJson.php';


class TaskJson extends AppModelJson {

    protected $_file = 'task.json';
    protected $_storage = STORAGE_TASK;
    protected $_index_file;

    /**
     * Constructor
     *
     * @param array $params - option to change some object variables, e.g. file, or storage
     */
    public function __construct($params = array()) {
        if(array_key_exists('file', $params)) {
            $this->_file = $params['file'];
        }

        if(array_key_exists('storage', $params)) {
            $this->_storage = $params['storage'];
        }

        $this->_index_file = $this->_storage . 'index.json';
    }

    /**
     * Adds spent time for a task
     *
     * @param int $id - id of the task
     * @param float $time - how much time has been spent
     * @return boolean
     */
    public function add_time($id, $time) {
        $error_msgs = array();
        $task_data = $this->get_data($id);

        if(!empty($task_data)) {
            if($task_data['task_status'] == 'opened') {
                $save_data = array(
                    'id' => $id,
                    'time_spent' => $task_data['time_spent'] + ($time  * 60));
                $this->_update($save_data);
            } else {
                $error_msgs['general'] = 'You can report only for opened tasks.';
            }
        } else {
            $error_msgs['general'] = 'Unknown Task.';    
        }

        $this->validation_errors = $error_msgs;
        return empty($error_msgs) ? true : false;
    }

    /**
     * Sets task_status to closed
     *
     * @param int $id - id of the task
     * @return boolean
     */
    public function close($id) {
        $error_msgs = array();
        $task_data = $this->get_data($id);

        if(!empty($task_data)) {
            if($task_data['task_status'] != 'closed') {
                $save_data = array(
                    'id' => $id,
                    'task_status' => 'closed');
                $this->_update($save_data);
            } else {
                $error_msgs['general'] = 'This task is already closed.';    
            }
        } else {
            $error_msgs['general'] = 'Unknown Task.';    
        }

        $this->validation_errors = $error_msgs;    
        return empty($error_msgs) ? true : false;    
    }

    /**
     * Find function.
     *
     * @param array $params - possible to specify additional parameters, e.g. fields
     * @return array - information about task that matches criteria.
     */
    public function find_all($params) {
        // Find all directories within storage folder.
        $folders = glob($this->_storage . '*', GLOB_ONLYDIR);


        // Find page id.
        $page = 0;         
        if(array_key_exists('page', $params)) {
            $page = 1;
            if(is_int($params['page']) && $params['page'] > 0) {
                $page = $params['page'];
            }
        }  

        // Find folders we can use on the page.
        $folders_4_page = array();
        if($page > 0) {
            $min = $page - 1 * ITEMS_PER_PAGE;
            $max = $page * ITEMS_PER_PAGE;

            for($min; $min <= $max; $min++) {
                if(array_key_exists($min, $folders)) {
                    $folders_4_page[] = $folders[$min];
                }
            }
        } else {
            $folders_4_page = $folders;
        }

        // Load data.
        $tasks_data = array();
        foreach($folders_4_page as $folder) {
            $task_id = substr($folder, strrpos($folder, DS) + 1);

            $task_data = $this->get_data($task_id);
            $tasks_data[] = $task_data;
        }        

        return $tasks_data;
    }

    /**
     * 
     *
     * @param 
     * @return 
     */
    public function get_data($task_id) {
        $task_data = array();
        $file_path = $this->_get_json_file($task_id);

        $content = file_get_contents($file_path);
        if(!empty($content)) {
            $task_data = json_decode($content);
        }

        return to_array($task_data);
    }

    /**
     * Sets task_status to opened
     *
     * @param int $id - id of the task
     * @return boolean
     */
    public function open($id) {
        $error_msgs = array();
        $task_data = $this->get_data($id);

        if(!empty($task_data)) {
            if($task_data['task_status'] != 'opened') {
                $save_data = array(
                    'id' => $id,
                    'task_status' => 'opened');
                $this->_update($save_data);
            } else {
                $error_msgs['general'] = 'This task is already opened.';    
            }
        } else {
            $error_msgs['general'] = 'Unknown Task.';    
        }

        $this->validation_errors = $error_msgs;    
        return empty($error_msgs) ? true : false;    
    }

    /**
     * Saves / updates new task into file
     *
     * @param int task_id - id of the task
     * @param array $data - data to be inserted
     * @return boolean .
     */
    public function save($data) {
        $id = null;

        if($this->validates($data)) {
            $data = $this->_complete_data($data);
            if(!array_key_exists('id', $data) || empty($data['id'])) {
                $id = $this->_insert($data);
            } else {
                $id = $this->_update($data);
            }
        }

        return $id;
    }

    /**
     * Validates data
     *
     * @param array $data - data to be validated
     * @return array $error_msg - array with error messages.
     */
    public function validates($data) {
        $error_msgs = array();

        if(empty($data)) {
            $error_msgs['general'] = 'Data are missing';
        }

        if(!array_key_exists('name', $data)) {
            $error_msgs['name'] = 'You forgot to insert task name';
        } else if(empty($data['name'])) {
            $error_msgs['name'] = 'You forgot to insert task name';
        } 

        $this->validation_errors = $error_msgs;
        return empty($error_msgs) ? true : false;
    }
}