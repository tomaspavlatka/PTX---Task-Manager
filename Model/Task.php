<?php 
require MODEL . 'AppModel.php';
class Task extends AppModel {

    protected $_table = 'tasks';
    private $_per_page = 25;

    public function __construct() {}

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

}
