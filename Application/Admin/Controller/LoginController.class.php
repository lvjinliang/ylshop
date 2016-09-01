<?php
namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {
    private $error = array();
    private $AdminUser = '';
    private $AdminUserLogin = '';

    public function _initialize() {
        header('Content-Type: text/html; charset=utf-8');
        $this->AdminUser = D('AdminUser');
        $this->AdminUserLogin = D('AdminUserLogin');
    }

    public function index () {
       $data = array();
       $data['name'] = '';
       $data['password'] = '';

       $redirect = I('post.redirect');
       if (empty($redirect)) {
           $redirect = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Admin/index/index');

           if (preg_match('/(login)/i', $redirect)) {
               $redirect = U('Admin/index/index');
           }
       }
       if (session('?user.id')) {
           redirect($redirect);
       }

       if (IS_POST && $this->validate_form()) {
           redirect($redirect);
       } else {
           $data['name'] = I('post.name');
           $data['password'] = I('post.password');
       }
       $data['error'] = $this->error;
       $this->assign('redirect', $redirect);
       $this->assign('data',$data);
       $this->display();
   }

    public function validate_form() {
        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '请输入用户名';
            return false;
        }

        if (!checkRequire(I('post.password'))) {
            $this->error['password'] = '请输入密码';
            return false;
        }

        if ((int)$this->AdminUserLogin->getLoginTotal(I('post.name')) >= 5) {
            $this->error['name'] = '登录错误次数达到5次，请联系管理员。';
            return false;
        }

        $checkLogin = $this->AdminUser->checkLogin(I('post.name'), I('post.password'));
        switch ($checkLogin) {
            case 1:
                $this->error['name'] = '请输入用户名';
                break;
            case 2:
                $this->error['password'] = '请输入密码';
                break;
            case 3:
                $this->error['name'] = '该用户名不存在';
                break;
            case 4:
                $this->error['password'] = '密码错误';
                break;
        }
        if ($checkLogin != 99) {
            $this->AdminUserLogin->addLoginTimes(I('post.name'));
        } else {
            $this->AdminUserLogin->resetLoginTimes(I('post.name'));
        }

        return (empty($this->error)) ? true : false;
    }


}