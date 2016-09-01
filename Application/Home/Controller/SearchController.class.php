<?php
namespace Home\Controller;
use Think\Controller;
class SearchController extends CommonController {
    private $Search = '';
    private $Goods = '';


    public function _initialize() {
        parent::_initialize();
        $this->Search = D('Search');
        $this->Goods = D('Goods');
    }

    public function index(){

        $search = I('get.search');
        $search = htmlspecialchars($search, ENT_QUOTES);

        $this->setTitle(C('SHOP_NAME').'|'.'搜索');
        $this->setKeywords('搜索');
        $this->setDescription('搜索');
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>'/');
        $breadcrumbs[] = array('title'=>'搜索','href'=>'javascript:void(0)');

        $filter = array();



        //分页
        $count = $this->Search->getTotal( $search);
        $page = setPage($count, C('PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $goodses = $this->Search->getList($filter, $search);
        $EventGoods = A('Goods', 'Event');
        $data['goods'] = $EventGoods->getGoods($goodses, 4,4,6,12);

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }
}