<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class ArticleModel extends CommonModel {
    protected $_auto = array(
        array('date_updated', 'time', 3, 'function'),
        array('date_added', 'time', 1, 'function')
    );
    public function getAll() {
        $data = $this->field('id,name')
                     ->where(array('status'=>1))
                     ->order(array('sort' => 'DESC', 'id' => 'DESC'))
                     ->select();
        return $data;
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
        $result = $this->field('a.*,ac.name cate_name')
                       ->alias('a')
                       ->join('LEFT JOIN __ARTICLE_CATEGORY__ ac ON a.category_id=ac.id')
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
                       ->join('LEFT JOIN __ARTICLE_CATEGORY__ ac ON a.category_id=ac.id')
                       ->where($filter)
                       ->count();
        return $result;
    }

}


?>