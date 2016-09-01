<?php
namespace Account\Controller;
use Think\Controller;
class IntegralController extends CommonController {

    private $error = array();
    public function _initialize() {
        parent::_initialize();

    }

    public function index() {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '我的积分', 'href' => 'javascript:void(0)');
        $data = array();
        $filter = array();
        $typeId = I('get.type_id');
        $typeId = empty($typeId) ? 1: $typeId;
        $typeId = !in_array($typeId, array(1,2))?1:$typeId;

        if( $typeId == 1 ) { //获取的积分列表
            $AccountIntegral = D('AccountIntegral');
            $count = $AccountIntegral->getTotal($filter);
            $page = setPage($count, C('PAGE_SIZE'));
            $filter['start'] = $page->firstRow;
            $filter['limit'] = $page->listRows;
            $data['show']  = $page->homeShow();// 分页显示输出
            $filter['order'] = 'ai.date_added DESC, ai.id DESC';
            $data['integralList'] = $AccountIntegral->getList($filter);
        } else if($typeId == 2) { //使用的积分列表
            $AccountIntegralUsed = D('AccountIntegralUsed');
            $count = $AccountIntegralUsed->getTotal($filter);
            $page = setPage($count, C('PAGE_SIZE'));
            $filter['start'] = $page->firstRow;
            $filter['limit'] = $page->listRows;
            $data['show']  = $page->homeShow();// 分页显示输出
            $filter['order'] = 'aiu.date_added DESC, aiu.id DESC';
            $data['integralList'] = $AccountIntegralUsed->getList($filter);
        }
        $data['typeId'] = $typeId;
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }






}