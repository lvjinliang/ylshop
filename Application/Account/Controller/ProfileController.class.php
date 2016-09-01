<?php
namespace Account\Controller;
use Think\Controller;
class ProfileController extends CommonController {
    private $Account;
    private $error = array();
    public function _initialize() {
        parent::_initialize();
        $this->Account = D('Account');
    }

    public function index() {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '用户信息', 'href' => 'javascript:void(0)');
        $data = array();
        if (IS_POST) {
            $data = I('post.');
            if ($this->validate_form()) {
                $this->Account->data($data)->save();
                $this->assign('success', '修改成功');
            } else {
                $this->assign('error', $this->error);
            }
        } else {
            $data = $this->accountObj->accountInfo;
        }

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }

    public function validate_form () {
        if (!checkRequire(I('post.username'))) {
            $this->error['username'] = '用户名不能为空';
        } else {
            if (!$this->Account->checkUnique('username', I('post.username'), array('id' => array('neq', I('post.id'))))) {
                $this->error['username'] = '用户名已存在';
            }
        }

        if (!checkEmail(I('post.email'))) {
            $this->error['email'] = '请输入正确的邮箱';
        } else {
            if (!$this->Account->checkUnique('email', I('post.email'), array('id' => array('neq', I('post.id'))))) {
                $this->error['email'] = '邮箱已存在';
            }
        }

        $telephone = I('post.telephone');
        if (!empty($telephone) && !checkTel($telephone)) {
            $this->error['telephone'] = '请输入正确的手机号码';
        }

        $home_phone = I('post.home_phone');
        if (!empty($home_phone) && !checkTelHome($home_phone)) {
            $this->error['home_phone'] = '请输入正确的电话号码';
        }

        return (empty($this->error)) ? true : false;
    }


}