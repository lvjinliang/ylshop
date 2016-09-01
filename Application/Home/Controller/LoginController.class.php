<?php
namespace Home\Controller;

use Think\Controller;

class LoginController extends CommonController {
    private $error = '';
    private $Account = '';
    private $AccountLogin = '';

    public function _initialize() {
        parent::_initialize();
        $this->Account = D('Account');
        $this->AccountLogin = D('AccountLogin');
    }

    public function index() {
        $redirect = I('post.redirect');
        if (empty($redirect)) {
            $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Home/index/index');
            $parse_referer = parse_url($redirect);
            //其它站开源则跳首页
            if (isset($parse_referer['host']) && !empty($parse_referer['host']) && !stripos(C('SHOP_URL'), $parse_referer['host'])) {
                $redirect = U('Home/index/index');
            }
            if (preg_match('/(login|register)/i', $redirect)) {
                $redirect = U('Home/index/index');
            }
        }


        if (session('?account.id')) {
            redirect($redirect);
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            redirect($redirect);
        } else {
            $breadcrumbs = array();
            $breadcrumbs[] = array('title' => '首页', 'href' => '/');
            $breadcrumbs[] = array('title' => '登录', 'href' => 'javascript:void(0)');
            $this->assign('breadcrumbs', $breadcrumbs);
            $this->assign('data', I('post.'));
            $this->assign('error', $this->error);
        }
        $this->assign('redirect', $redirect);
        $this->display();
    }

    private function validate_form() {

        if (!checkRequire(I('post.username'))) {
            $this->error['username'] = '请输入用户名';
            return false;
        }

        if (!checkRequire(I('post.password'))) {
            $this->error['password'] = '请输入密码';
            return false;
        }

        if (!check_verify(I('post.verify'))) {
            $this->error['verify'] = '请输入正确的验证码';
            return false;
        }

        if ((int)$this->AccountLogin->getLoginTotal(I('post.username')) >= 5) {
            $this->error['username'] = '登录错误次数达到5次，今日不能再登录。';
            return false;
        }

        $checkLogin = $this->Account->checkLogin(I('post.username'), I('post.password'));
        switch ($checkLogin) {
            case 1:
                $this->error['username'] = '请输入用户名';
                break;
            case 2:
                $this->error['password'] = '请输入密码';
                break;
            case 3:
                $this->error['username'] = '该用户名末注册或末激活';
                break;
            case 4:
                $this->error['password'] = '密码错误';
                break;
        }
        if ($checkLogin != 99) {
            $this->AccountLogin->addLoginTimes(I('post.username'));
        } else {
            $this->AccountLogin->resetLoginTimes(I('post.username'));
        }

        return (empty($this->error)) ? true : false;
    }

    public function logout() {
        $this->accountObj->logout();
        $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Home/index/index');
        redirect($redirect);

    }

}