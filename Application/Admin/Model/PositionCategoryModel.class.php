<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class PositionCategoryModel extends Model {
    public function getPositionByCategoryId($category_id) {
        $data = $this->field('position_id')->where(array('category_id' => $category_id))->select();
        $position_ids = array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $position_ids[] = $val['position_id'];
            }
        }
        return $position_ids;
    }
}


?>