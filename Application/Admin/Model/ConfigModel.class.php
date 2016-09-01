<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class ConfigModel extends Model {
    public function getAll() {
        $result = $this->select();
        $data = array();
        foreach($result as $key => $val){
            $data[$val['key']] = $val['value'];
        }
        return $data;
    }

    public function addConfig($data) {
        if(empty($data)){ return false;}

        $sql = "REPLACE INTO __PREFIX__config(`key`, `value`) VALUES(";
        foreach($data as $key => $val ){
            $sql .= "'{$key}','{$val}'),(";
        }
        $sql = rtrim($sql, ',(');
        $result = $this->execute($sql);
        return $result;
    }

}


?>