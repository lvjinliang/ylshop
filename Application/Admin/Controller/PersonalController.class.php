<?php
namespace Admin\Controller;
use Think\Controller;

class PersonalController extends CommonController {

    protected $error = array();
    public $adminUser = '';

    public function _initialize() {
        parent::_initialize();
        $this->adminUser = D('AdminUser');
    }


    public function index() {
        $this->setTitle('个人信息');

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->adminUser->create();
            if(!empty($this->adminUser->password)){
                $this->adminUser->salt = $this->adminUser->getSalt();
                $this->adminUser->password = $this->adminUser->getPassword($this->adminUser->password,$this->adminUser->salt);
            } else {
                unset($this->adminUser->password );
            }
            if ($this->adminUser->save()!==false) {
                $this->assign('success','修改成功');

            } else {
                $this->assign('errorInfo', $this->adminUser->getDbError());
            }
        }
        $this->form();
    }

    public function validate_form() {

        if (checkRequire(I('post.password'))) {
            if (!checkRequire(I('post.old_password'))) {
                $this->error['old_password'] = '请输入旧密码';
            } else {
                $result = $this->adminUser->checkLogin(session('user.name'),I('post.old_password'));
                if ($result!= 99) {
                    $this->error['old_password'] = '旧密码错误';
                }
            }

            //存在则验证
            if(I('post.password') ){
                if (!checkFormat('/^.{6,16}$/i', I('post.password') )) {
                    $this->error['password'] = '由6到16位字符组成';
                }
            }
        }

        if (!checkConfirm(I('post.re_password'), I('post.password') )) {
            $this->error['re_password'] = '两次密码不一致';
        }

        return (empty($this->error)) ? true : false;
    }



    public function form() {
        $id = session('user.id');
        $data = $this->adminUser->getDataById($id);
        $data['password'] = '';
        $data['re_password'] = '';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'个人信息','href'=>U('personal/index'));
        $this->assign('data', $data);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}