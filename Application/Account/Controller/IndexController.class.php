<?php
namespace Account\Controller;
use Think\Controller;
class IndexController extends CommonController {
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
        $breadcrumbs[] = array('title' => '个人中心', 'href' => 'javascript:void(0)');
        $data = array();
        $data['username'] = $this->accountObj->accountInfo['username'];
        $data['money'] = $this->accountObj->accountInfo['money'];
        $data['pay_integral'] = $this->accountObj->getIntergral();
        $data['last_time'] = date('Y-m-d H:i:s',$this->accountObj->accountInfo['last_time']);
        $data['login_times'] = $this->accountObj->accountInfo['login_times'];
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }


}