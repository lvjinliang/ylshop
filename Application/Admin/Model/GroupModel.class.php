<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class GroupModel extends CommonModel {

    function getRulesById ($id) {
        $result = $this->field('rules')->where(array('id'=>$id))->find();
        return $result['rules'];
    }

    function updateRulesById($id, $rules) {
        $result = $this->where(array('id'=>$id))->save($rules);
        return $result===false?false:true;
    }


}


?>