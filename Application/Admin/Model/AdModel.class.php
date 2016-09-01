<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AdModel extends CommonModel {
    protected $_auto = array(
        array('date_updated', 'time', 3, 'function'),
        array('date_added', 'time', 1, 'function'),

    );

    public function addAd($data) {
        $ad_id = $this->add();
        if ($ad_id) {
            //添加广告图片
            if (!empty($data['image'])) {
                $AdImage = D('AdImage');
                $dataImage = array();
                foreach ($data['image'] as $key => $val) {
                    $dataImage[$key]['ad_id'] = $ad_id;
                    $dataImage[$key]['url'] = $val;
                    $dataImage[$key]['title'] = $data['image_title'][$key];
                    $dataImage[$key]['sort'] = $data['image_sort'][$key];
                    $dataImage[$key]['link'] = $data['image_link'][$key];
                }
                $AdImage->addAll($dataImage);
            }
        }
        return $ad_id;
    }

    public function saveAd($data) {
        $result = $this->save();

        if ($result !== false) {
            //添加广告图片
            $AdImage = D('AdImage');
            $AdImage->where(array('ad_id' => $data['id']))->delete();
            if (!empty($data['image'])) {
                $AdImage = D('AdImage');
                $dataImage = array();
                foreach ($data['image'] as $key => $val) {
                    $dataImage[$key]['ad_id'] = $data['id'];
                    $dataImage[$key]['url'] = $val;
                    $dataImage[$key]['title'] = $data['image_title'][$key];
                    $dataImage[$key]['sort'] = $data['image_sort'][$key];
                    $dataImage[$key]['link'] = $data['image_link'][$key];
                }
                $AdImage->addAll($dataImage);
            }
        }
        return $result;
    }

    public function getDataById($id) {
        $condition['id'] = $id;
        $data = $this->where($condition)->limit(0, 1)->find();

        //相册
        $AdImage = D('AdImage');
        $image = $AdImage->getImageByAdId($id);
        $data['image'] = $image['url'];
        $data['image_title'] = $image['title'];
        $data['image_sort'] = $image['sort'];
        $data['image_link'] = $image['link'];
        return $data;
    }


}


?>