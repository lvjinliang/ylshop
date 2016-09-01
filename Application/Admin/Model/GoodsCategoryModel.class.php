<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class GoodsCategoryModel extends Model {
    public function getCategoryByGoodsId($goods_id) {
        $data = $this->alias('gc')
                     ->field('gc.category_id,gc.is_primary,c.path')
                     ->join('__CATEGORY__ c ON gc.category_id=c.id')
                     ->where(array('goods_id' => $goods_id))
                     ->select();

        $category = array('category_ids'=>array(), 'is_primary'=>'', 'category_name'=>array());
        if (!empty($data)) {
            foreach ($data as $val) {
                $category['category_ids'][] = $val['category_id'];
                $category['category_name'][] = $this->getPathName($val['path'].','.$val['category_id']);
                if($val['is_primary'] == 1) {
                    $category['is_primary'] = $val['category_id'];
                }
            }
        }
        return $category;
    }

    public function getPathName ($path) {
        $sql = "SELECT 'aa',
                GROUP_CONCAT(name ORDER BY path ASC separator '>' ) path_name
	            FROM __PREFIX__category
	            WHERE id IN({$path})
	            GROUP BY 'aa'
	            LIMIT 0,1";
        $data = $this->query($sql);
        return $data[0]['path_name'];
    }

}


?>