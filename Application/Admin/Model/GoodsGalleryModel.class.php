<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class GoodsGalleryModel extends CommonModel {

    public function getGalleryByGoodsId($goods_id) {
        $data = $this->field('url,title,sort')
                     ->where(array('goods_id' => $goods_id))
                     ->order(array('sort'=>'desc','id'=>'desc'))
                     ->select();
        $gallerys = array('url'=>array(), 'title'=>array(), 'sort'=>array());
        if (!empty($data)) {
            foreach ($data as $val) {
                $gallerys['url'][] = $val['url'];
                $gallerys['title'][] = $val['title'];
                $gallerys['sort'][] = $val['sort'];
            }
        }
        return $gallerys;
    }

}


?>