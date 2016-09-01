<?php
namespace Admin\Controller;
use Think\Controller;

class ConfigController extends CommonController {
    protected $error = array();
    public $Config = '';

    public function _initialize() {
        parent::_initialize();
        $this->Config = D('Config');
    }

    public function index() {
        //查询条件
        $this->setTitle('全站设置');
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'全站设置','href'=>U('config/index'));

        $data = array();
        if(IS_POST){
            $result = $this->Config->addConfig(I('post.'));
        }
        $data = $this->Config->getAll();
        $data['logo'] = empty($data['logo'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['logo'];
        $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('config/index');


        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);

        $this->assign('data', $data);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('redirect', $redirect);
        $this->display();
    }

}