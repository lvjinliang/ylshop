<?php
namespace Admin\Controller;

use Think\Controller;

class CategoryController extends CommonController {

    protected $error = array();
    public $Category = '';

    public function _initialize() {
        parent::_initialize();
        $this->Category = D('Category');
    }

    public function index() {
        //查询条件
        $this->setTitle('商品分类');
        $filter = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'商品分类','href'=>U('category/index'));

        //排序
        $filter['order'] = array('sort'=>'DESC','id'=>'DESC');
        $lists = $this->Category->getLists($filter);

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
        $this->setTitle('添加商品分类');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            if ($this->Category->addCategory(I('post.'))) {
                session('success','新增成功');
                redirect(U('category/index',array('pid'=>I('post.pid'))));
            } else {
                $this->error('新增失败');
                $this->assign('errorInfo', $this->Category->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        $this->setTitle('编辑商品分类');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('category/index');
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {

            if ($this->Category->saveCategory(I('post.'))!==false) {
                session('success','修改成功');
                redirect($redirect);
            } else {
                $this->assign('errorInfo', $this->Category->getDbError());
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
            $this->error['name'] = '商品分类名不能为空';
        }
        if (!checkNumber(I('post.sort'), 0, 5)) {
            $this->error['sort'] = '排序为数值';
        }
        if (!checkNumber(I('post.grade'), 0, 10)) {
            $this->error['grade'] = '1';
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
            $allCategory = $this->Category->getAll();
            $sonCategory = array();

            foreach(explode(',', $id) as $val) {
                $sonCategory = array_merge($sonCategory, getLevel($allCategory, 2, $val));
            }
            $ids = array();
            foreach($sonCategory as $k=>$v){
                $ids[] = $v['id'];
            }
            $ids = implode(',',$ids);
            if($this->Category->where(array('id'=>array('in',$ids)))->delete()!==false){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = '该分类下有商品不能删除';
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
                $data = $this->Category->getDataById($id);
                $PositionCategory = D('PositionCategory');
                $Attribute = D('Attribute');
                $data['position'] = $PositionCategory->getPositionByCategoryId($id);
                $data['filterAttr'] = $Attribute->getAttrByIds($data['filter_attr']);
            } else {
                $data['name'] = '';
                $data['pid'] = '0';
                $data['show_in_nav'] = '1';
                $data['position'] = array();
                $data['filter_attr'] = '';
                $data['status'] = '1';
                $data['grade'] = '0';
                $data['sort'] = '0';
                $data['keywords'] = '';
                $data['description'] = '';
            }
        }
        //推荐位
        $Position = D('Position');
        $GoodsType = D('GoodsType');
        $data['allPosition'] = $Position->getAll();

        if(!empty($id)){
            $data['categorys'] = $this->Category->getFilterChildrenCategory($id);
        } else {
            $data['categorys'] = $this->Category->getCategoryLevel();
        }


        $data['goodsTypes'] = $GoodsType->getAll();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'商品分类','href'=>U('category/index'));

        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('category/index');
        }

        $this->assign('data', $data);
        $this->assign('redirect', $redirect);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('form');
    }


}