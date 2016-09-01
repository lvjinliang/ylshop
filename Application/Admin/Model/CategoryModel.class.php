<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class CategoryModel extends CommonModel {
    protected $_auto = array (
        array('filter_attr','changeAttr',3,'callback'),
    );

    public function changeAttr() {
        $filterAttr =  func_get_arg(0);
        $filterAttr = array_filter(array_unique($filterAttr));
        return implode(',', $filterAttr);
    }

    public function getAll() {
        $data = $this->field('id,name,pid')
                     ->where(array('status'=>1))
                     ->order(array('sort' => 'DESC', 'id' => 'DESC'))
                     ->select();
        return $data;
    }

    public function getFilterChildrenCategory($id) {
        $data = $this->getAll();
        $childrenData = getLevel($data, 2, $id);
        $childrenIds = array();
        foreach($childrenData as $val) {
            $childrenIds[] = $val['id'];
        }
        foreach($data as $key => $val) {
            if(in_array($val['id'], $childrenIds)){
                unset($data[$key]);
            }
        }
        $data = getLevel($data, 2);
        return $data;

    }

    public function getCategoryLevel() {
        $data = $this->getAll();
        if (!empty($data)) {
            $data = getLevel($data, 2);
        }

        return $data;
    }

    public function getPathById($id) {
        $data = $this->field('path')
                     ->where(array('id'=>$id))
                     ->find();
        if(empty($data)|| empty($data['path'])){$data['path']=0;}
        return $data['path'];
    }

    public function addCategory($data) {
        if($data['pid']==0) {
            $data['path'] = 0;
        } else {
            $path = $this->getPathById($data['pid']);
            $data['path'] = $path.','.$data['pid'];
        }
        $this->create($data);
        $category_id = $this->add();
        if ($category_id) {
            //添加推荐位
            $PositionCategory = D('PositionCategory');
            if (!empty($data['position'])) {
                $dataPosition = array();
                foreach($data['position'] as $key => $val) {
                    $dataPosition[$key]['category_id'] = $category_id;
                    $dataPosition[$key]['position_id'] = $val;
                }
                $PositionCategory->addAll($dataPosition);
            }
        }
        return $category_id;
    }

    public function saveCategory($data) {
        if($data['pid']==0) {
            $data['path'] = 0;
        } else {
            $path = $this->getPathById($data['pid']);
            $data['path'] = $path.','.$data['pid'];
        }
        $this->create($data);
        $result = $this->save();
        if($result!==false) {
            //更改推荐位
            $PositionCategory = D('PositionCategory');
            $PositionCategory->where(array('category_id'=>$data['id']))->delete();
            if (!empty($data['position'])) {
                $dataPosition = array();
                foreach($data['position'] as $key => $val) {
                    $dataPosition[$key]['category_id'] = $data['id'];
                    $dataPosition[$key]['position_id'] = $val;
                }
                $PositionCategory->addAll($dataPosition);
            }
        }

        return $result;
    }

    public function getLists($filter = array()) {
        $order = isset($filter['order']) ? $filter['order'] : array();
        $result = $this->order($order)->select();
        if (!empty($result)) {
            $data = getLevel($result, 2);
        } else {
            $data = array();
        }
        return $data;
    }
}


?>