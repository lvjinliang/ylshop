<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class GroupUserModel extends CommonModel {
    /*protected $_auto = array (
        array('date_updated','getDateTime',3,'callback'),
        array('date_added','getDateTime',1,'callback')
    );*/


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
        $result = $this->alias('gu')
                       ->join('LEFT JOIN __ADMIN_USER__ au ON gu.uid=au.id')
                       ->where($filter)->order($order)->limit($start, $limit)->select();

        return $result;
    }


    public function getTotal($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->alias('gu')
                       ->join('LEFT JOIN __ADMIN_USER__ au ON gu.uid=au.id')
                       ->where($filter)
                       ->count();
        return $result;
    }

    public function isAuthUser($data = array()) {
        $total = $this->where($data)->count();
        return $total?true:false;
    }

}


?>