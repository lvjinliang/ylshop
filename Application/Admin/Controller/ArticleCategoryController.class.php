<?php
namespace Admin\Controller;

use Think\Controller;

class ArticleCategoryController extends CommonController {

    protected $error = array();
    public $ArticleCategory = '';

    public function _initialize() {
        parent::_initialize();
        $this->ArticleCategory = D('ArticleCategory');
    }

    public function index() {
        //查询条件
        $this->setTitle('文章分类');
        $filter = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'文章分类','href'=>U('article_category/index'));

        //排序
        $filter['order'] = array('sort'=>'DESC','id'=>'DESC');
        $lists = $this->ArticleCategory->getLists($filter);

        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {
        $this->setTitle('添加文章分类');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            if ($this->ArticleCategory->addCategory(I('post.'))) {
                session('success','新增成功');
                redirect(U('article_category/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->ArticleCategory->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑文章分类');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('article_category/index');
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            if ($this->ArticleCategory->saveCategory(I('post.'))!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->ArticleCategory->getDbError());
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
            $this->error['name'] = '文章分类名不能为空';
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
            $allCategory = $this->ArticleCategory->getAll();
            $sonCategory = array();
            foreach(explode(',', $id) as $val) {
                $sonCategory = array_merge($sonCategory, getLevel($allCategory, 2, $val));
            }
            $ids = array();
            foreach($sonCategory as $k=>$v){
                $ids[] = $v['id'];
            }
            $ids = implode(',',$ids);
            if($this->ArticleCategory->where(array('id'=>array('in',$ids)))->delete()!==false){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '该分类下有文章不能删除';
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
                $data = $this->ArticleCategory->getDataById($id);
            } else {
                $data['name'] = '';
                $data['pid'] = '0';
                $data['status'] = '1';
                $data['sort'] = '0';
                $data['keywords'] = '';
                $data['description'] = '';
            }
        }

        if(!empty($id)){
            $data['categorys'] = $this->ArticleCategory->getFilterChildrenCategory($id);
        } else {
            $data['categorys'] = $this->ArticleCategory->getCategoryLevel();
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'文章分类','href'=>U('article_category/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('article_category/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }


}