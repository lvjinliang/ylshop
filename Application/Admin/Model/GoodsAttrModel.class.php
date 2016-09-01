<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class GoodsAttrModel extends CommonModel {

    public function getAttrValueByGoodsId($goodsId,$goodsTypeId) {
        $filter = array();
        $filter['ga.goods_id'] = array('eq', $goodsId);
        $filter['g.goods_type_id'] = array('eq', $goodsTypeId);
        $attrData = $this->alias('ga')
                         ->field('ga.*')
                         ->join("__GOODS__ g ON ga.goods_id=g.id")
                         ->where($filter)
                         ->select();
        $data = array();
        foreach($attrData as $val) {
            $data[$val['attr_id']][] = $val;
        }
        return $data;
    }


}


?>