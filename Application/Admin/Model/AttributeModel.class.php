<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AttributeModel extends CommonModel {

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
        $result = $this->field('a.*,b.name type_name')
            ->alias('a')
            ->join('LEFT JOIN __GOODS_TYPE__ b ON a.type_id=b.id')
            ->where($filter)->order($order)->limit($start, $limit)->select();
        return $result;
    }

    public function getTotal($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->alias('a')
            ->join('LEFT JOIN __GOODS_TYPE__ b ON a.type_id=b.id')
            ->where($filter)->count();
        return $result;
    }

    //用于商品分类
    public function getSearchAttriByTypeId($typeId) {
        $result = $this->field('id, name')
            ->where(array('type_id' => $typeId, 'index' => array('neq', 1), 'input_type'=>'2'))
            ->order(array('sort' => 'DESC'))
            ->select();
        return $result;
    }

    public function getAttrByTypeId($typeId) {
        $result = $this->where(array('type_id' => $typeId))
            ->order(array('sort' => 'DESC'))
            ->select();
        $data = array();
        foreach($result as $key => $val ){
            $data[$key] = $val;
            $val['values'] = explode("\r\n", trim($val['values']));
            $data[$key]['values'] = $val['values'];
        }

        return $data;
    }

    public function getAttrByIds($ids){
        $result = $this->alias('a')
                       ->field('gt.id, gt.name, a.id attr_id, a.name attr_name')
                       ->join('left join __GOODS_TYPE__ gt ON a.type_id=gt.id')
                       ->where(array('a.id'=>array('in', $ids)))
                       ->order(array('a.sort' => 'DESC'))
                       ->select();
        echo $this->getDbError();
        return $result;
    }

}


?>