<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class BrandModel extends CommonModel {
    public function getAll() {
        $data = $this->field('id,name')
                     ->where(array('status'=>1))
                     ->order(array('sort' => 'DESC', 'id' => 'DESC'))
                     ->select();
        return $data;
    }

    public function getNameById($id) {
        $data = $this->field('name')
            ->where(array('id'=>$id))
            ->find();
        return empty($data)?'':$data['name'];
    }

}


?>