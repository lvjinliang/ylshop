<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AccountRankModel extends CommonModel {
    public function getAll() {
        $data = $this->field('id,name')
                     ->where(array('status'=>1))
                     ->order(array('sort' => 'DESC', 'id' => 'DESC'))
                     ->select();
        return $data;
    }

    public function getLists($filter = array()) {
        //设置排序
        $order = isset($filter['order']) ? $filter['order'] : array();
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->where($filter)->order($order)->select();
        return $result;
    }

}


?>