<?php
/**
 * User: 良子
 * Date: 16-03-02
 */

namespace Home\Model;
use Think\Model;

class CategoryModel extends Model {
    private $allCategory = '';
    public function __construct() {
        parent::__construct();
        $this->allCategory = $this->getAll();
    }

    private function getAll() {
        $data = $this->where(array('status'=>1))
                     ->order(array('sort' => 'DESC', 'id' => 'DESC'))
                     ->select();

        return $data;
    }

    public function getCategory() {
        $data = $this->allCategory;
        if (!empty($data)) {
            $data = getLevel($data, 1);
        }
        return $data;
    }

    public function getCategoryPathById($id) {
        $pathId = $this->getPathById($id);
        $path = $this->field('id,name')
                     ->where(array('status'=>'1','id'=>array('in', $pathId)))
                     ->select();
        return $path;
    }

    public function getPathById($id) {
        $data = $this->field('path')
            ->where(array('id'=>$id))
            ->find();

        if(empty($data)|| empty($data['path'])) {
            $data['path'] = $id;
        } else {
            $data['path'] = $data['path'].','.$id;
        }

        return $data['path'];
    }

    public function getChildrenById($id) {
        $data = $this->allCategory;
        if (!empty($data)) {
            $data = getLevel($data, 2, $id);
        }
        return $data;
    }

    public function getChildrenIDbyId($id) {
        $children = $this->getChildrenById($id);
        $ids = array();
        foreach($children as $key => $val ){
            $ids[] = $val['id'];
        }
        return $ids;
    }

    public function getCategoryInfoById($id) {
        $sql = "SELECT * FROM __PREFIX__category WHERE id='{$id}' limit 0,1";
        $categoryData = $this->query($sql);

        if(!empty($categoryData) && !empty($categoryData[0]['filter_attr'])) {
            //搜索
            $categoryData[0]['search'] = array();
            $sql = "SELECT `id`, `name`, `values`
                    FROM __PREFIX__attribute a
                    WHERE a.id in({$categoryData[0]['filter_attr']}) AND a.input_type=2";
            $attribute = $this->query($sql);
            foreach($attribute as $key => $val){
                if(!empty($val['values'])){
                    $attribute[$key]['values'] = explode(PHP_EOL, $val['values']);
                }
            }
            if(!empty($attribute)){
                $categoryData[0]['search'] = $attribute;
            }
        }
        return $categoryData[0];
    }




}
?>