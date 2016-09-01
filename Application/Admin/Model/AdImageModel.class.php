<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AdImageModel extends CommonModel {

    public function getImageByAdId($ad_id) {
        $data = $this->field('url,title,sort,link')
                     ->where(array('ad_id' => $ad_id))
                     ->order(array('sort'=>'desc','id'=>'desc'))
                     ->select();
        $images = array('url'=>array(), 'title'=>array(), 'sort'=>array());
        if (!empty($data)) {
            foreach ($data as $val) {
                $images['url'][] = $val['url'];
                $images['title'][] = $val['title'];
                $images['sort'][] = $val['sort'];
                $images['link'][] = $val['link'];
            }
        }
        return $images;
    }

}


?>