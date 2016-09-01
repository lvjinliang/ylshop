<?php
namespace Admin\Controller;
use Think\Controller;

class AdController extends CommonController {
    protected $error = array();
    public $Ad = '';

    public function _initialize() {
        parent::_initialize();
        $this->Ad = D('Ad');
    }

    public function index() {
        $this->setTitle('广告管理');
        //查询条件
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'广告管理','href'=>U('ad/index'));
        if(I('get.name')) {
            $filter['name'] = array('like', I('get.name').'%');
            $search['name'] = I('get.name');
        } else {
            $search['name'] = '';
        }
        //排序
        $filter['order'] = array( 'id'=>'DESC');
        //分页
        $count = $this->Ad->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->Ad->getLists($filter);
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
        $this->setTitle('添加广告');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Ad->create();
            if ($this->Ad->addAd(I('post.'))) {
                session('success','新增成功');
                redirect(U('ad/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->Ad->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑广告');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('ad/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Ad->create();
            if ($this->Ad->saveAd(I('post.'))!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Ad->getDbError());
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
        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '广告名不能为空';
        } else {
            if (!$this->Ad->checkUnique('name', I('post.name'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['name'] = '广告名已存在';
            }
        }

        if (!checkRequire(I('post.code'))) {
            $this->error['code'] = 'code不能为空';
        } else {
            if (!$this->Ad->checkUnique('code', I('post.code'),array('id'=> array('neq',I('post.id'))))) {
                $this->error['code'] = 'code已存在';
            }
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
            if($this->Ad->where(array('id'=>array('in',$id)))->delete()!==false){
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
        $attribute = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->Ad->getDataById($id);
            } else {
                $data['name'] = '';
                $data['code'] = '';
                $data['status'] = 1;
                $data['image'] = array();
            }
        }
        $data['attribute_html'] = '';

        if(!empty($data['goods_type_id'])) {
            $data['attribute_html'] = $this->get_attr($data['id'],$data['goods_type_id'],$attribute);
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'广告列表','href'=>U('ad/index'));


        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('ad/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }




}