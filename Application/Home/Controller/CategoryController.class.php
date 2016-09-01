<?php
namespace Home\Controller;
use Think\Controller;
class CategoryController extends CommonController {
    private $Category  = '';
    public function _initialize() {
        parent::_initialize();
        $this->Category = D('Category');
    }

    public function index(){
        $id = I('get.id');
        if(empty($id)){
            $this->_empty();
            exit();
        }
        $categoryInfo = $this->Category->getCategoryInfoById($id);
        if(empty($categoryInfo)) {
            $this->_empty('此分类页不存在');
            exit();
        }
        $this->setTitle(C('SHOP_NAME').'|'.$categoryInfo['name']);
        $this->setKeywords($categoryInfo['keywords']);
        $this->setDescription($categoryInfo['description']);
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>'/');
        $path = $this->Category->getCategoryPathById($id);
        foreach($path as $key => $val ) {
            $breadcrumbs[] = array('title'=>$val['name'],'href'=>U('Home/category/index',array('id'=>$val['id'])));
        }

        $data = array();
        $data['id'] = $id;
        $data['leftCategory'] = $this->Category->getChildrenById($path[0]['id']);
        $filter = array();
        $Goods = D('Goods');
        $Brand = D('Brand');
        $filter['order'] = 'gc.is_primary DESC, g.sort DESC, g.id DESC';

        //列表筛选条件
        $data['search'] = array();
        $data['search']['brand'] = $Brand->getBrand();
        array_unshift($data['search']['brand'],array('name'=>'全部','id'=>0));
        $data['search']['attr'] = $categoryInfo['search'];
        $buildQuery= array();
        $buildQuery['id'] = $id;

        //属性选择
        $filterAttr = array();
        foreach($data['search']['attr'] as $val) {
            $buildQuery['attr_'.$val['id']] =  I('get.attr_'.$val['id']);
            $buildQuery['attr_'.$val['id']] =  empty($buildQuery['attr_'.$val['id']])?0:$buildQuery['attr_'.$val['id']];
            if(!empty($buildQuery['attr_'.$val['id']])){
                $filterAttr['attr_id'][$val['id']] = $val['id'];
                $filterAttr['attr_value'][$val['id']] = $buildQuery['attr_'.$val['id']];
            }
        }
        $fliterGoodsIds = $Goods->getGoodsIdByFilterAttr($filterAttr);


        foreach($data['search']['brand'] as $key => $val) {
            $buildQuery['brand_id'] = $val['id'];
            $data['search']['brand'][$key]['url'] = U('Home/category/index', $buildQuery );
        }

        $buildQuery['brand_id'] = $filter['g.brand_id'] = I('get.brand_id');

        foreach($data['search']['attr'] as $key => $val) {
            foreach($val['values'] as $k => $v) {
                $tmpBuildQuery = $buildQuery;
                $tmpBuildQuery['attr_'.$val['id']] = $v;
                $data['search']['attr'][$key]['url'][$k] = U('Home/category/index', $tmpBuildQuery );
            }
            array_unshift($data['search']['attr'][$key]['values'], '全部');
            $tmpBuildQuery['attr_'.$val['id']] = 0;
            array_unshift($data['search']['attr'][$key]['url'], U('Home/category/index', $tmpBuildQuery ));
        }

        foreach($data['search']['brand'] as $key=>$val){
            if($val['id'] == $buildQuery['brand_id']){
                $data['search']['brand'][$key]['isSelected'] = 1;
            } else {
                $data['search']['brand'][$key]['isSelected'] = 0;
            }
        }

        foreach($data['search']['attr'] as $key=>$val){
            foreach($val['values'] as $k => $v) {
                if($v==='全部'){
                    if($buildQuery['attr_'.$val['id']] === 0){
                        $data['search']['attr'][$key]['isSelected'][$k]  = 1;
                    } else {
                        $data['search']['attr'][$key]['isSelected'][$k]  = 0;
                    }
                } else {
                    if($buildQuery['attr_'.$val['id']] === $v){
                        $data['search']['attr'][$key]['isSelected'][$k]  = 1;
                    } else {
                        $data['search']['attr'][$key]['isSelected'][$k]  = 0;
                    }
                }

            }
        }
        //分页
        $count = $Goods->getTotalByCategoryId($id, $filter, $fliterGoodsIds);
        $page = setPage($count, C('PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $goodses = $Goods->getGoodsByCategoryId($id, $filter, $fliterGoodsIds);
        $EventGoods = A('Goods', 'Event');
        $data['goods'] = $EventGoods->getGoods($goodses, 4,4,6,12);
        $data['buildQuery'] = $buildQuery;

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }
}