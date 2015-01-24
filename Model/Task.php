<?php 
require MODEL . 'AppModel.php';
class Task extends AppModel {

    public $validation_errors = array();    
    protected $_table = 'tasks';

    public function __construct() {}

    /**
     * Adds spent time for a task
     *
     * @param int $id - id of the task
     * @param float $time - how much time has been spent
     * @return boolean
     */
    public function add_time($id, $time) {
        $error_msgs = array();
        $task_data = $this->get_data($id, array('fields' => array('task_status', 'time_spent')));

        if(!empty($task_data)) {
            if($task_data['task_status'] == 'opened') {
                $save_data = array(
                    'time_spent' => $task_data['time_spent'] + ($time  * 60));
                dibi::query('UPDATE %n', $this->_table, 'SET', $save_data, 'WHERE [id] = %i', $id);
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
        $task_data = $this->get_data($id, array('fields' =>  'task_status'));

        if(!empty($task_data)) {
            if($task_data['task_status'] != 'closed') {
                $save_data = array(
                    'task_status' => 'closed');
                $query = sprintf('UPDATE [%s] SET', $this->_table);        
                dibi::query($query, $save_data, 'WHERE [id] = %i', $id);
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
     * Returns data for specific task
     *
     * @param int $id - id of the task
     * @param array $params - possible to specify additional params, e.g. fields
     * @return array $task_data - data of selected task
     */
    public function get_data($id, $params = array()) {
        $select_columns = $this->get_select_columns($params);
        $query = sprintf('SELECT %s FROM [%s] ', $select_columns, $this->_table);
        $query .= sprintf('WHERE [id] = %d', $id);

        $result = dibi::query($query);
        return to_array($result->fetch());
    }

    /**
     * Find function.
     *
     * @param array $params - possible to specify additional parameters, e.g. fields
     * @return array - information about task that matches criteria.
     */
    public function find_all($params) {
        $select_columns = $this->get_select_columns($params);
        $query = sprintf('SELECT %s FROM [%s] ', $select_columns, $this->_table);

        if(array_key_exists('conditions', $params) && is_array($params['conditions'])) {

            $query .= 'WHERE ';
            $conditions = array();
            foreach($params['conditions'] as $key => $value) {
                if(!is_array($value)) {
                    $conditions[$key] = mysql_real_escape_string(trim($value));
                } else {
                    $conditions[$key] = $value;
                }
            }

            $flag_condition = false;
            if(array_key_exists('keywords', $conditions) && !empty($conditions['keywords'])) {
                $flag_condition = true;
                $keywords = $conditions['keywords'];
                $query .= sprintf("([name] LIKE '%%%s%%' OR [content] LIKE '%%%s%%') ", $keywords, $keywords);
            }

            if(array_key_exists('task_status', $conditions)) {
                if($flag_condition) {
                    $query .= 'AND ';
                }
                $query .= sprintf("[task_status] = '%s' ", $conditions['task_status']);
                
                $flag_condition = true;
            }

            if(array_key_exists('status', $conditions)) {
                if($flag_condition) {
                    $query .= 'AND ';
                }
                if(is_array($conditions['status'])) {
                    $query .= sprintf('[status] IN (%s) ', implode(',', $conditions['status']));
                } else {
                    $query .= sprintf('[status] = %d ', $conditions['status']);
                }

                $flag_condition = true;
            }

            if(!$flag_condition) {
                $query .= '1 ';
            }
        }

        if(array_key_exists('order', $params)) {            
            $order_key = key($params['order']);
            $query .= sprintf('ORDER BY [%s] %s ', $order_key, $params['order'][$order_key]);
        }

        if(array_key_exists('page', $params)) {
            $page = 1;
            if(is_int($params['page']) && $params['page'] > 0) {
                $page = $params['page'];
            }

            $offset = ($page - 1) * ITEMS_PER_PAGE;
            $query .= sprintf('LIMIT %d OFFSET %d', ITEMS_PER_PAGE, $offset);
        }        

        $result = dibi::query($query);
        $data = $result->fetchAll();        
        
        return $data;
    }

    /**
     * Sets task_status to opened
     *
     * @param int $id - id of the task
     * @return boolean
     */
    public function open($id) {
        $error_msgs = array();
        $task_data = $this->get_data($id, array('fields' =>  'task_status'));

        if(!empty($task_data)) {
            if($task_data['task_status'] != 'opened') {
                $save_data = array(
                    'task_status' => 'opened');
                $query = sprintf('UPDATE [%s] SET', $this->_table);        
                dibi::query($query, $save_data, 'WHERE [id] = %i', $id);
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
     * Saves / updates new task into db
     * system will check whether there is a field id in $data and whether is not empty. If both conditions
     * are met, system will update the result instead of inserting new one
     *
     * @param array $data - data to be inserted
     * @return integer $id - id of the task.
     */
    public function save($data) {
        $id = null;

        if($this->validates($data)) {
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

    /**
     * Inserting new task into database
     * 
     * @param array $data - array with data to be inserted
     * @return int $id - id of new inserted task
     */
    private function _insert($data) {

        // 1. Insert new task.
        $data['status'] = 1;
        $data = $this->_complete_data($data);
        dibi::query(sprintf('INSERT INTO [%s]', $this->_table), $data);
        
        // 2. Get its id.
        $result = dibi::query(sprintf('SELECT LAST_INSERT_ID() from [%s]', $this->_table));
        $id = $result->fetchSingle();

        return $id;
    }

    /**
     * Updating task into database
     * 
     * @param array $data - array with data to be inserted
     * @return int $id - id of new inserted task
     */
    private function _update($data) {
        $id = $data['id'];
        unset($data['id']);

        // 1. Insert new task.
        $data = $this->_complete_data($data);
        dibi::query(sprintf('UPDATE [%s] SET', $this->_table), $data, 'WHERE [id] = %i', $id);        

        return $id;
    }
}
