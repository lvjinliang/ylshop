<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AttributeModel extends Model {

    public function getAttributeByTypeId($typeId) {
        $sql = "SELECT *
                FROM __PREFIX__attribute
                WHERE type_id='{$typeId}'
                ORDER BY sort DESC, id DESC";
        $attrs = $this->query($sql);
        $data = array();
        foreach($attrs as $key => $val ) {
            $data[$val['id']] = $val;
        }
        return $data;
    }

}


?>