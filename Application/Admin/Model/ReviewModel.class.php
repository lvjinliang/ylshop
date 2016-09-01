<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class ReviewModel extends CommonModel {
    public function getLists($filter = array()) {
        //设置分页
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('ADMIN_PAGE_SIZE');
        $limit = empty($limit) ? C('ADMIN_PAGE_SIZE') : $limit;

        //设置排序
        $order = isset($filter['order']) ? $filter['order'] : array();
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->field('r.*, g.name,g.goods_sn')
                       ->alias('r')
                       ->join('LEFT JOIN __GOODS__ g ON r.goods_id=g.id')
                       ->where($filter)
                       ->order($order)
                       ->limit($start, $limit)
                       ->select();
        return $result;
    }

    public function getTotal($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->alias('r')
                       ->join('LEFT JOIN __GOODS__ g ON r.goods_id=g.id')
                       ->where($filter)->count();
        return $result;
    }

    public function getDataById($id) {
        $condition['r.id'] = $id;
        $data = $this->field('r.*,g.name,g.goods_sn,a.username')
                     ->alias('r')
                     ->join('LEFT JOIN __GOODS__ g ON r.goods_id=g.id')
                     ->join('LEFT JOIN __ACCOUNT__ a ON a.id=r.account_id')
                     ->where($condition)->find();
        return $data;
    }

}


?>