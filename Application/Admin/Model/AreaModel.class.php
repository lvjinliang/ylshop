<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AreaModel extends CommonModel {

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
        $result = $this->field('a.*,IF(ISNULL(b.name),\'顶级区域\',b.name) p_name')
                       ->alias('a')
                       ->join('LEFT JOIN __AREA__ b ON a.pid=b.id' )
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
        $result = $this->alias('a')
                       ->join('LEFT JOIN __AREA__ b ON a.pid=b.id' )
                       ->where($filter)
                       ->count();
        return $result;
    }

    public function getPnameByPid($pid) {
        $condition['id'] = $pid;
        $data = $this->field('name')->where($condition)->find();
        return $data['name'];
    }

    public function getAll(){
        $data = $this->field('id,name,pid')
                     ->where(array('status'=>1))
                     ->order(array('sort'=>'DESC','id'=>'DESC'))
                     ->select();
        return $data;
    }





}


?>