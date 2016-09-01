<?php
namespace Admin\Controller;

use Think\Controller;

class EmailTplController extends CommonController {

    protected $error = array();
    public $EmailTpl = '';

    public function _initialize() {
        parent::_initialize();
        $this->EmailTpl= D('EmailTpl');
    }

    public function index() {
        //查询条件
        $this->setTitle('邮件模板');
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'邮件模板','href'=>U('email_tpl/index'));
        if(I('get.title')) {
            $filter['title'] = array('like', I('get.title').'%');
            $search['title'] = I('get.title');
        } else {
            $search['title'] = '';
        }

        //排序
        $filter['order'] = array(  'id'=>'DESC');

        //分页
        $count = $this->EmailTpl->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->EmailTpl->getLists($filter);

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
        $this->setTitle('添加邮件模板');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->EmailTpl->create();
            if ($this->EmailTpl->add()) {
                session('success','新增成功');
                redirect(U('email_tpl/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->EmailTpl->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑邮件模板');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('email_tpl/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->EmailTpl->create();
            if ($this->EmailTpl->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->EmailTpl->getDbError());
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

        if (!checkRequire(I('post.title'))) {
            $this->error['title'] = '标题不能为空';
        } else {
            if (!$this->EmailTpl->checkUnique('title', I('post.title'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['title'] = '标题已存在';
            }
        }
        if (!checkRequire(I('post.content'))) {
            $this->error['content'] = '内容不能为空';
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
            if($this->EmailTpl->where(array('id'=>array('in',$id)))->delete()!==false){
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
                $data = $this->EmailTpl->getDataById($id);
            } else {
                $data['title'] = '';
                $data['content'] = '';
                $data['note'] = '';
            }
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'邮件模板','href'=>U('email_tpl/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('email_tpl/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}