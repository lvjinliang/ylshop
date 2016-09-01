<?php
namespace Admin\Controller;
use Think\Controller;

class SearchController extends CommonController {
    protected $error = array();
    public $Goods = '';

    public function _initialize() {
        parent::_initialize();
        $this->Goods = D('Goods');
    }


    public function index () {
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'生成搜索词','href'=>'javascript:void(0)');
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display();
    }

    public function ajax_create_keys () {
        //排序
        $filter['order'] = array( 'g.sort'=>'DESC', 'g.id'=>'DESC');

        //分页
        $count = $this->Goods->getTotal($filter);
        //$page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $page = setPage($count, 2);

        $p = I('get.p');
        $p = empty($p) ? 1 : $p;

        if ($p>ceil($count/2)) {
            $json = array('success'=>2, 'msg'=>'搜索词生成成功');
            $this->ajaxReturn($json);
            exit();
        }

        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $lists = $this->Goods->getSearchKeysLists($filter);
        $msg = '';
        foreach ($lists as $key=>$val) {
            $goods_id = $val['id'];
            $keywords = $val['name'].' ';
            $keywords .= $val['category_name'].' ';
            $keywords .= $val['brand_name'].' ';
            $keywords .= str_replace(',', ' ' , $val['attr_value']).' ';
            $keywords .= str_replace(array(',','|'), ' ' , $val['keywords']);
            $sql = "REPLACE INTO __SEARCH__ SET
                    goods_id='{$goods_id}',
                    keywords='{$keywords}'";
            $Search = M('Search');
            $Search->execute($sql);
            $msg .= "商品id：{$goods_id}搜索词生成成功\r\n";
        }
        $json = array('success'=>1, 'msg'=>$msg, 'p'=>$p+1);
        $this->ajaxReturn($json);
        exit();
    }

}