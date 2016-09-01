<?php
namespace Admin\Controller;
use Think\Controller;

class UserController extends CommonController {

    protected $error = array();
    public $adminUser = '';

    public function _initialize() {
        parent::_initialize();
        $this->adminUser = D('AdminUser');
    }

    public function index() {
        $this->setTitle('用户管理');
        //查询条件
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'用户管理','href'=>U('user/index'));
        if(I('get.name')) {
            $filter['name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }
        if(I('get.status')!=='') {
            $filter['status'] = array('eq', I('get.status'));
            $search['status'] = I('get.status');
        } else {
            $search['status'] = '';
        }
        //排序
        $filter['order'] = array( 'id'=>'DESC');

        //分页
        $count = $this->adminUser->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->adminUser->getLists($filter);

        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('show', $show);
        $this->assign('search', $search);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加用户');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->adminUser->create();
            $this->adminUser->salt = $this->adminUser->getSalt();
            $this->adminUser->password = $this->adminUser->getPassword($this->adminUser->password, $this->adminUser->salt);

            if ($this->adminUser->add()) {
                session('success','新增成功');
                redirect(U('user/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->adminUser->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑用户');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('user/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->adminUser->create();
            if(!empty($this->adminUser->password)){
                $this->adminUser->salt = $this->adminUser->getSalt();
                $this->adminUser->password = $this->adminUser->getPassword($this->adminUser->password,$this->adminUser->salt);
            } else {
                unset($this->adminUser->password );
            }
            if ($this->adminUser->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->adminUser->getDbError());
                $this->form();
            }
        } else {
            $id = I('get.id');
            if (empty($id)) {
                session('error','非法操作');
                redirect($redirect);
            }
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function validate_form() {
        if (!checkFormat('/^[A-Za-z_]{1}\w{3,16}$/i', I('post.name') )) {
            $this->error['name'] = '只能由字母,下划线和数字组成的4到16位';
        } else {
            if (!$this->adminUser->checkUnique('name', I('post.name'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['name'] = '用户名已存在';
            }
        }

        if(I('post.id')){
            //修改存在则验证
            if(I('post.password') ){
                if (!checkFormat('/^.{6,16}$/i', I('post.password') )) {
                    $this->error['password'] = '由6到16位字符组成';
                }
            }
        } else {
            //添加必需验证
            if (!checkFormat('/^.{6,16}$/i', I('post.password') )) {
                $this->error['password'] = '由6到16位字符组成';
            }
        }


        if (!checkConfirm(I('post.re_password'), I('post.password') )) {
            $this->error['re_password'] = '两次密码不一致';
        }

        return (empty($this->error)) ? true : false;
    }

    public function delete() {
        $json = array('success'=>1,'msg'=>'');
        $id = I('post.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法传操作';
        } else {
            if($this->adminUser->where(array('id'=>array('in',$id)))->delete()!==false){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '删除失败';
            }
        }
        $this->ajaxReturn($json);
    }

    public function form() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->adminUser->getDataById($id);
                $data['password'] = '';
                $data['re_password'] = '';
            } else {
                $data['name'] = '';
                $data['truename'] = '';
                $data['password'] = '';
                $data['re_password'] = '';
                $data['sex'] = '1';
                $data['birthday'] = '';
                $data['status'] = '1';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'用户管理','href'=>U('user/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('user/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}