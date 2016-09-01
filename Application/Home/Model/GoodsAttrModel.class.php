<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class GoodsAttrModel extends Model {

    public function getGoodsAttrPrice ($goodsId, $attrIds, $attrValues) {
        $sql = "SELECT attr_id, attr_value, attr_price
                FROM __PREFIX__goods_attr ga
                WHERE ga.goods_id='{$goodsId}' AND ga.attr_id in({$attrIds})";
        $query = $this->query($sql);
        $attrIds = explode(',', $attrIds);
        $attrValues = explode(',', $attrValues);
        $price = 0;
        if (!empty($query)) {
            foreach ($attrIds as $key => $aId) {
                foreach ($query as $val) {
                    if($val['attr_id'] ==$aId && $val['attr_value'] == $attrValues[$key]) {
                        $price += $val['attr_price'];
                        break;
                    }

                }
            }

        }
        return $price;
    }

}


?>