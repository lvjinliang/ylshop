<?php
namespace Home\Controller;

use Think\Controller;

class RegisterController extends CommonController {
    private $error = '';
    private $Account = '';

    public function _initialize() {
        parent::_initialize();
        $this->Account = D('Account');
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

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Account->create();
            $accountId = $this->Account->register(I('post.'));
            if ($accountId) {
                if (C('MAIL_REGISTER_VERIFY')) {
                    redirect(U('Home/register/reg_verify'));
                } else {
                    redirect($redirect);
                }
            } else {
                \Think\Log::write($this->Account->getDBError(), 'ERROR', '', C('LOG_PATH') . 'ylshop.log');
                $this->assign('errorInfo', '系统错误，请联系管理员！');
            }
        } else {
            $breadcrumbs = array();
            $breadcrumbs[] = array('title' => '首页', 'href' => '/');
            $breadcrumbs[] = array('title' => '注册', 'href' => 'javascript:void(0)');
            $this->assign('breadcrumbs', $breadcrumbs);
            $this->assign('data', I('post.'));
            $this->assign('error', $this->error);
        }
        $this->assign('redirect', $redirect);
        $this->display();
    }

    public function reg_verify() {
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '注册', 'href' => U('Home/register/index'));
        $breadcrumbs[] = array('title' => '等待验证', 'href' => 'javascript:void(0)');
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display();
    }

    public function check() {
        $code = I('get.code');
        if (empty($code)) {
            return false;
        }
        $code = \Think\Crypt::decrypt(base64_decode(urldecode($code)), C('ENCRYPT_KEY'));
        $EmailVerify = D('EmailVerify');
        switch ($EmailVerify->verifyCode($code)) {
            case 1:
                $msg = '非法验证码';
                break;
            case 2:
                $msg = '验证码失效';
                break;
            case 3:
                $msg = '该验证码已验证';
                break;
            case 4:
                $msg = '验证成功';
                break;
            case 5:
                $msg = '系统错误';
                break;
            default:
                $msg = '系统错误';
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '验证结果', 'href' => 'javascript:void(0)');
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('msg', $msg);
        $this->display();
    }

    private function validate_form() {
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

        if (!checkRequire(I('post.password'))) {
            $this->error['password'] = '请输入密码';
        } else {
            if (!checkFormat('/^\S{6,10}$/', I('post.password'))) {
                $this->error['password'] = '请输入6到10位字符串';
            }
        }

        if (!checkRequire(I('post.re_password'))) {
            $this->error['re_password'] = '请输入重复密码';
        }

        if (!checkConfirm(I('post.password'), I('post.re_password'))) {
            $this->error['re_password'] = '两次密码输入不一至';
        }

        if (!check_verify(I('post.verify'))) {
            $this->error['verify'] = '请输入正确的验证码';
        }
        $agree = I('post.agree');
        if (empty($agree)) {
            $this->error['agree'] = '请仔细阅读并同意我们的注册协议';
        }


        return (empty($this->error)) ? true : false;
    }


}