<?php
namespace Admin\Controller;

use Think\Controller;

class ArticleController extends CommonController {

    protected $error = array();
    public $Article = '';

    public function _initialize() {
        parent::_initialize();
        $this->Article = D('Article');
    }

    public function index() {
        //查询条件
        $this->setTitle('文章管理');
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'文章管理','href'=>U('article/index'));
        if(I('get.title')) {
            $filter['a.title'] = array('like', I('get.title').'%');
            $search['title'] = I('get.title');
        } else {
            $search['title'] = '';
        }

        //排序
        $filter['order'] = array( 'a.sort'=>'DESC', 'a.id'=>'DESC');

        //分页
        $count = $this->Article->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show  = $page->adminShow();// 分页显示输出
        $lists = $this->Article->getLists($filter);

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
        $this->setTitle('添加文章');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Article->create();
            $this->Article->user_id = session('user.id');
            if ($this->Article->add()) {
                session('success','新增成功');
                redirect(U('article/index',array('pid'=>I('post.pid'))));
            } else {
                $this->assign('errorInfo', $this->Article->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑文章');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('article/index');
        }
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->Article->create();
            $this->Article->user_id = session('user.id');
            if ($this->Article->save()!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Article->getDbError());
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
            $this->error['title'] = '文章名不能为空';
        }
        if (!checkRequire(I('post.category_id'))) {
            $this->error['category_id'] = '请选择分类';
        }
        if (!checkNumber(I('post.sort'), 0, 5)) {
            $this->error['sort'] = '排序为数值';
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
            if($this->Article->where(array('id'=>array('in',$id)))->delete()!==false){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '服务器异常';
            }
        }
        $this->ajaxReturn($json);
    }

    public function form() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
            $data['thumb'] = empty($data['thumb'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['thumb'];
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->Article->getDataById($id);
                $data['thumb'] = empty($data['thumb'])?PUBLIC_PATH.'Admin/images/no_image-100x100.png':$data['thumb'];
            } else {
                $data['title'] = '';
                $data['thumb'] = PUBLIC_PATH.'Admin/images/no_image-100x100.png';
                $data['content'] = '';
                $data['sort'] = '1';
                $data['status'] = '1';
                $data['keywords'] = '';
                $data['description'] = '';
            }
        }
        $ArticleCategory = D('ArticleCategory');
        $data['categorys'] = $ArticleCategory->getCategoryLevel();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'文章管理','href'=>U('article/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('article/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }
}