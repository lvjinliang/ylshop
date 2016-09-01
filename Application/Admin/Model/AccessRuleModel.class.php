<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AccessRuleModel extends CommonModel {

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
        $result = $this->field('a.*,IF(ISNULL(b.title),\'顶级菜单\',b.title) p_title')
                       ->alias('a')
                       ->join('LEFT JOIN __ACCESS_RULE__ b ON a.pid=b.id' )
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
                       ->join('LEFT JOIN __ACCESS_RULE__ b ON a.pid=b.id' )
                       ->where($filter)
                       ->count();
        return $result;
    }

    public function getPtitleByPid($pid) {
        $condition['id'] = $pid;
        $data = $this->field('title')->where($condition)->find();
        return $data['title'];
    }

    public function getAll(){
        $data = $this->field('id,title,pid')
                     ->where(array('status'=>1))
                     ->order(array('sort'=>'DESC','id'=>'DESC'))
                     ->select();
        return $data;
    }

    public function getMenu(){
        $data = $this->where(array('status'=>1,'is_show'=>1))
                     ->order(array('sort'=>'DESC','id'=>'DESC'))
                     ->select();
        return $data;
    }

    public function getMenuByNames($names){
        $data = $this->where(array('status'=>1,'is_show'=>1,'name'=>array('in',$names)))
            ->order(array('sort'=>'DESC','id'=>'DESC'))
            ->select();

        return $data;
    }

}


?>