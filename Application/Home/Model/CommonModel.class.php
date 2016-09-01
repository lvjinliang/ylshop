<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Home\Model;
use Think\Model;

class CommonModel  extends Model {

    /*
     * @function 检测字段唯一性
     * @param string $field 字段名
     * @param string $value 字段值
     * @param array  $param 过滤字段 array('fieldName'=>'value')
     * @return boolen {false:'不唯一',true:'唯一'}
     *
     */
    public function checkUnique($field, $value, $param = array()) {
        if(!empty($param)){
            foreach($param as $key=>$val) {
                if(!empty($val)){
                    $condition[$key] = $val;
                }
            }
        }
        $condition[$field] = $value;
        $result = $this->where($condition)->count($field);
        return empty($result)?true:false;
    }


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
        $result = $this->where($filter)->order($order)->limit($start, $limit)->select();
        return $result;
    }

    public function getTotal($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->where($filter)->count();
        return $result;
    }

    protected function filter($data = array()){
        foreach($data as $key => $val) {
            //过滤商品列表中的是否删除
            if(empty($val) && $key!='g.is_delete') {
                unset($data[$key]);
            }
        }
        return $data;
    }


    public function getDataById($id) {
        $condition['id'] = $id;
        $data = $this->where($condition)->find();
        return $data;
    }



} 