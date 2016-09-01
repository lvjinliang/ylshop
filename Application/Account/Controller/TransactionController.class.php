<?php
namespace Account\Controller;
use Think\Controller;
class TransactionController extends CommonController {
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
        $breadcrumbs[] = array('title' => '我的资金', 'href' => 'javascript:void(0)');
        $data = array();
        $filter = array();

        $AccountTransaction = D('AccountTransaction');
        $count = $AccountTransaction->getTotal($filter);
        $page = setPage($count, C('PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $filter['order'] = 'at.date_added DESC, at.id DESC';
        $data['transactionList'] = $AccountTransaction->getList($filter);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }






}