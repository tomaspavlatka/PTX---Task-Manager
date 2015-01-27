<?php 

abstract class AppModelJson {

    public $validation_errors = array();

    protected $_file = null;
    protected $_storage;

    protected function _complete_data($data) {
        // 1. Updated field.
        if(!array_key_exists('updated', $data)) { // Add info about updated.
            $data['updated'] = date('Y-m-d H:i:s');
        }

        // 2. If we insert new record
        if(!array_key_exists('id', $data) || empty($data['id'])) {
            if(!array_key_exists('created', $data)) { // Add info about created.
                $data['created'] = date('Y-m-d H:i:s');
            }            
        }

        if(!array_key_exists('time_spent', $data)) { // Add info about time_spent.
            $data['time_spent'] = 0;
        }

        if(!array_key_exists('task_status', $data)) { // Add info about task_status.
            $data['task_status'] = 'opened';
        }

        if(!array_key_exists('status', $data)) { // Add info about status.
            $data['status'] = 1;
        }

        return $data;
    }

    protected function _get_folder($id) {
        $folder_path = $this->_storage . $id;

        // If we do not have folder, create it.
        if(!is_dir($folder_path)) {
            mkdir($folder_path, 0755);
        }

        return $folder_path;
    }

    protected function _get_id_from_index() {
        $id = 1;

        $index_content = json_decode(file_get_contents($this->_index_file));
        if(!empty($index_content)) {
            if(array_key_exists('last_id', $index_content)) {
                $id = ++$index_content->last_id;
            }
        }
        
        return $id;
    }

    protected function _get_json_file($id) {
        $folder_path = $this->_get_folder($id);
        $file_path = $folder_path . DS . $this->_file;
        if(!file_exists($file_path)) {
            $handler = fopen($file_path, 'w+');
            fwrite($handler, null);
            fclose($handler);
        }

        return $file_path;
    }

    protected function _insert($data) {
        $error_msgs = array();
        $id = $this->_get_id_from_index();
        $file_path = $this->_get_json_file($id);

        if($handler = fopen($file_path, 'w+')) {
            $data['id'] = $id;
            if(fwrite($handler, json_encode($data))) {
                if(fclose($handler)) {

                    $this->_write_id_2_index($id);
                    return $id;
                } else {
                    $error_msgs = array(
                        'file' => 'Creating file error.');
                }        
            } else {
                $error_msgs = array(
                    'file' => 'Creating file error.');
            } 
        } else {
            $error_msgs = array(
                'file' => 'Creating file error.');
        } 

        $this->validation_errors = $error_msgs;
    }

    protected function _update($data) {
        $error_msgs = array();

        $id = $data['id'];
        $current_data = $this->get_data($id);

        foreach($data as $key => $value) {
            if(array_key_exists($key, $current_data)) {
                $current_data[$key] = $value;
            }
        }

        $file_path = $this->_get_json_file($id);

        if($handler = fopen($file_path, 'w+')) {            
            if(fwrite($handler, json_encode($current_data))) {
                if(fclose($handler)) {                    
                    return $id;
                } else {
                    $error_msgs = array(
                        'file' => 'Creating file error.');
                }        
            } else {
                $error_msgs = array(
                    'file' => 'Creating file error.');
            } 
        } else {
            $error_msgs = array(
                'file' => 'Creating file error.');
        } 

        $this->validation_errors = $error_msgs;
    }

    protected function _write_id_2_index($id) {
        $index_content = json_decode(file_get_contents($this->_index_file));
        if(!empty($index_content)) {
            $index_content->last_id = $id;
        } else {
            $index_content = array(
                'last_id' => $id);
        }

        if($handler = fopen($this->_index_file, 'w+')) {
            if(fwrite($handler, json_encode($index_content))) {
                if(fclose($handler)) {
                    return true;
                }
            }
        }
    }
}