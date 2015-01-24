<?php 
require MODEL . 'AppModel.php';
class Task extends AppModel {

    public $validation_errors = array();
    private $_per_page = 25;
    protected $_table = 'tasks';

    public function __construct() {}

    /**
     * Returns data for specific task
     *
     * @param int $task_id - id of the task
     * @param array $params - possible to specify additional params, e.g. fields
     * @return array $task_data - data of selected task
     */
    public function get_data($task_id, $params = array()) {
        $select_columns = $this->get_select_columns($params);
        $query = sprintf('SELECT %s FROM [%s] ', $select_columns, $this->_table);
        $query .= sprintf('WHERE [id] = %d', $task_id);

        $result = dibi::query($query);
        return to_array($result->fetch());
    }

    /**
     * 
     *
     * @param 
     * @return 
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

            $offset = ($page - 1) * $this->_per_page;
            $query .= sprintf('LIMIT %d OFFSET %d', $this->_per_page, $offset);
        }        

        $result = dibi::query($query);
        $data = $result->fetchAll();        
        
        return $data;
    }

    /**
     * Saves / updates new task into db
     * system will check whether there is a field id in $data and whether is not empty. If both conditions
     * are met, system will update the result instead of inserting new one
     *
     * @param array $data - data to be inserted
     * @return integer $task_id - id of the task.
     */
    public function save($data) {
        $task_id = null;

        if($this->validates($data)) {
            if(!array_key_exists('id', $data) || empty($data['id'])) {
                $task_id = $this->_insert($data);
            } else {
                $task_id = $this->_update($data);
            }
        }

        return $task_id;
    }

    /**
     * Validates data
     *
     * @param array $data - data to be validated
     * @return array $error_msg - array with error messages.
     */
    public function validates($data) {
        $this->validation_errors = array();

        if(empty($data)) {
            $this->validation_errors['general'] = 'Data are missing';
        }

        if(!array_key_exists('name', $data)) {
            $this->validation_errors['name'] = 'You forgot to insert task name';
        } else if(empty($data['name'])) {
            $this->validation_errors['name'] = 'You forgot to insert task name';
        } 

        return empty($this->validation_errors);
    }

    /**
     * Inserting new task into database
     * 
     * @param array $data - array with data to be inserted
     * @return int $task_id - id of new inserted task
     */
    private function _insert($data) {

        // 1. Insert new task.
        $data['status'] = 1;
        $data = $this->_complete_data($data);
        dibi::query(sprintf('INSERT INTO [%s]', $this->_table), $data);
        
        // 2. Get its id.
        $result = dibi::query(sprintf('SELECT LAST_INSERT_ID() from [%s]', $this->_table));
        $task_id = $result->fetchSingle();

        return $task_id;
    }
}
