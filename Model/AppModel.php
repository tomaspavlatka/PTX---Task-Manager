<?php 

class AppModel {

    protected $_table = null;
    

    public function get_select_columns($params) {
        $select_columns = '*';
        if(array_key_exists('fields', $params) && !empty($params['fields'])) {

            if(is_array($params['fields'])) {
                $select_columns = null;
                foreach($params['fields'] as $field) {
                    $select_columns .= '['. $field .'],';
                }

                $select_columns = substr(trim($select_columns), 0, -1);
            } else {
                $select_columns = sprintf('[%s]', $params['fields']);
            }
        }

        return $select_columns;
    }
}