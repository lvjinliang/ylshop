<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class DayCountVisitModel extends CommonModel {

    public function getLists($filter = array(), $type='') {
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
        if (empty($type)) {
            $result = $this->where($filter)->order($order)->limit($start, $limit)->select();
        } else {
            $result = $this->where($filter)->order($order)->select();
        }

        return $result;
    }
}


?>